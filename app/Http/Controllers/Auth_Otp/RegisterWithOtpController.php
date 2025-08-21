<?php

namespace App\Http\Controllers\Auth_Otp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\updateDeviceTokenRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\Auth\UserResource;
use App\Http\Resources\PatientResource;
use App\Http\Resources\PatientRecordResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterWithOtpController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }
    /*  public function register1(RegisterRequest $registerRequest)
     {
         $language = $this->parseLanguageHeader($registerRequest->header('Accept-Language'));
         app()->setLocale($language);

         // تأكد أن البريد غير مستخدم فعلاً
         if (User::where('email', $registerRequest->email)->exists()) {
             return response()->json([
                 'message' => __('auth.email_used'),
             ], 409);
         }

         $otp = rand(100000, 999999);

         EmailOtp::updateOrCreate(
             ['email' => $registerRequest->email],
             [
                 'otp' => $otp,
                 'expired_at' => Carbon::now()->addMinutes(10)
             ]
         );
         //  Mail::to($registerRequest->email)->send(new EmailOtpMail($otp));
         // 3. حفظ البيانات المؤقتة بدون كلمة المرور
         $registerRequest->session()->put('register_email', $registerRequest->email);
         $registerRequest->session()->put('register_name', $registerRequest->name);
         $registerRequest->session()->put('register_password', Hash::make($registerRequest->password));
         $registerRequest->session()->put('register_phone', $registerRequest->phone);



         return response()->json([
             'message' => __('auth.otp_sent'),
             'email' => $registerRequest->email,
             'name' => $registerRequest->name,
             'phone' => $registerRequest->phone,
             'password' => bcrypt($registerRequest->password), // يمكن تخزينه مؤقتًا في التطبيق
             'language' => $language,
         ]);
     } */
    public function register(RegisterRequest $request)
    {
        $language = $this->parseLanguageHeader($request->header('Accept-Language'));
        app()->setLocale($language);

        $response = $this->authService->initiateRegistration($request->validated(), $language);

        if (!$response['status']) {
            return response()->json(['message' => $response['message']], 409);
        }

        return response()->json([
            'message' => $response['message'],
            'data' => $response['data']
        ]);
    }
    /* public function verifyOtpStore(Request $request)
    {
        $language = $this->parseLanguageHeader($request->header('Accept-Language'));
        app()->setLocale($language);
        //dd($language);
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $email = $request->session()->get('register_email');
        $name = $request->session()->get('register_name');
        $password = $request->session()->get('register_password');
        $phone = $request->session()->get('register_phone');

        $emailotp = EmailOtp::where('email', $email)
            ->where('otp', $request->otp)
            ->where('expired_at', '>=', Carbon::now())
            ->first();
        if (!$emailotp) {
            return response()->json([
                'message' => __('auth.invalid_otp'),
            ], 422);
        }
        // dd('otp verify successfully');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'language' => $language,
            'phone' => $phone,
            //   'role' => 'patient',
        ]);
        $user->refresh(); // لإعادة تحميل الخصائص من قاعدة البيانات، ومنها role

        $patient = null;

        // إذا كان المريض، أنشئ سجل المريض
        if ($user->role === 'patient') {
            $patient = Patient::create([
                'user_id' => $user->id,
                'photo' => null, // أو يمكنك إضافة صورة من الجلسة لاحقًا
            ]);
            // 1. إنشاء سجل طبي مرتبط بالمريض الجديد
            $patient_record = Patient_record::create(['patient_id' => $patient->id]);

        }
        // اجعل اللغة المعتمدة الحالية هي التي اختارها المستخدم
        app()->setLocale($user->language ?? config('app.fallback_locale', 'en'));
        $token = $user->createToken('auth_token')->plainTextToken;

        $emailotp->delete();
        // حذف البيانات من الجلسة
        $request->session()->forget([
            'register_email',
            'register_name',
            'register_password',
            'register_phone',
        ]);


        Auth::login($user);

        return response()->json([
            'message' => __('auth.register_success'),
            'user' => $user,
            'patient' => $patient,
            'patient_record' => $patient_record,
            'token' => $token,
        ]);
    } */

    public function verifyOtpStore(VerifyOtpRequest $request)
    {
        $language = $this->parseLanguageHeader($request->header('Accept-Language'));
        App::setLocale($language);

        $response = $this->authService->verifyOtp($request->otp, $language);

        if (!$response['status']) {
            return response()->json([
                'message' => $response['message'],
            ], 422);
        }


        return response()->json([
            'message' => $response['message'],
            'user' => new UserResource($response['user']),
            'patient' => $response['patient'] ? new PatientResource($response['patient']) : null,
            //'patient_record' => $response['patient_record'] ? new PatientRecordResource($response['patient_record']) : null,
            'token' => $response['token'],
        ]);

    }
    public function resendOtp(Request $request)
    {
        $result = $this->authService->resendOtp();

        return response()->json([
            'message' => $result['message'],
        ], $result['code']);
    }

    protected function parseLanguageHeader(?string $header): string
    {
        if (!$header)
            return config('app.fallback_locale', 'en');

        $locales = explode(',', $header);
        $primary = trim(explode(';', $locales[0])[0]);
        $lang = strtolower(substr($primary, 0, 2));

        return in_array($lang, ['ar', 'en']) ? $lang : config('app.fallback_locale', 'en');
    }
    //to set tokenfor notification
    public function updateDeviceToken(updateDeviceTokenRequest $request)
    {//dd($request);
        $user = auth()->user();
        $user->fcm_token = $request->token;
        if ($user->fcm_token) {
            $user->fcm_token = $request->token;
            $user->update();
        }
        $user->save();

        return response()->json(['message' => 'Device token updated successfully']);
    }


}
