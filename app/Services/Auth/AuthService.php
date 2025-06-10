<?php
namespace App\Services\Auth;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Resources\Auth\UserResource;
use App\Mail\EmailOtpMail;
use App\Models\EmailOtp;
use App\Models\Patient;
use App\Models\Patient_record;
use App\Models\User;
use App\Repositories\Eloquent\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;

class AuthService
{
    public function __construct(protected UserRepository $userRepo)
    {
    }

    /*  public function handleRegistration(RegisterRequest $request): JsonResponse
     {
         $language = $this->parseLanguageHeader($request->header('Accept-Language'));
         app()->setLocale($language);

         if ($this->userRepo->existsByEmail($request->email)) {
             return response()->json(['message' => __('auth.email_used')], 409);
         }

         $otp = rand(100000, 999999);
         EmailOtp::updateOrCreate(
             ['email' => $request->email],
             ['otp' => $otp, 'expired_at' => Carbon::now()->addMinutes(10)]
         );

         // Mail::to($request->email)->send(new EmailOtpMail($otp)); // افعلها عند الحاجة

         $request->session()->put([
             'register_email' => $request->email,
             'register_name' => $request->name,
             'register_password' => Hash::make($request->password),
             'register_phone' => $request->phone,
         ]);

         return response()->json([
             'message' => __('auth.otp_sent'),
             'email' => $request->email,
             'name' => $request->name,
             'phone' => $request->phone,
             'language' => $language,
         ]);
     }

     public function handleOtpVerification(VerifyOtpRequest $request): JsonResponse
     {
         $language = $this->parseLanguageHeader($request->header('Accept-Language'));
         app()->setLocale($language);

         $email = $request->session()->get('register_email');
         $otpRecord = EmailOtp::where('email', $email)
             ->where('otp', $request->otp)
             ->where('expired_at', '>=', Carbon::now())
             ->first();

         if (!$otpRecord) {
             return response()->json(['message' => __('auth.invalid_otp')], 422);
         }

         $user = $this->userRepo->create([
             'name' => $request->session()->get('register_name'),
             'email' => $email,
             'password' => $request->session()->get('register_password'),
             'language' => $language,
             'phone' => $request->session()->get('register_phone'),
         ]);

         $patient = $user->role === 'patient'
             ? Patient::create(['user_id' => $user->id])
             : null;

         $record = $patient
             ? Patient_record::create(['patient_id' => $patient->id])
             : null;

         $token = $user->createToken('auth_token')->plainTextToken;

         $otpRecord->delete();
         $request->session()->forget([
             'register_email', 'register_name', 'register_password', 'register_phone'
         ]);

         Auth::login($user);

         return response()->json([
             'message' => __('auth.register_success'),
             'user' => new UserResource($user),
             'token' => $token,
             'patient' => $patient,
             'patient_record' => $record,
         ]);
     }

     private function parseLanguageHeader(?string $header): string
     {
         if (!$header) return config('app.fallback_locale', 'en');

         $locales = explode(',', $header);
         $lang = strtolower(substr(trim(explode(';', $locales[0])[0]), 0, 2));

         return in_array($lang, ['ar', 'en']) ? $lang : config('app.fallback_locale', 'en');
     } */
    public function initiateRegistration(array $data, string $language): array
    {
        if ($this->userRepo->existsByEmail($data['email'])) {
            return [
                'status' => false,
                'message' => __('auth.email_used')
            ];
        }

        $otp = rand(100000, 999999);
        EmailOtp::updateOrCreate(
            ['email' => $data['email']],
            ['otp' => $otp, 'expired_at' => Carbon::now()->addMinutes(10)]
        );

         // Mail::to($data['email'])->send(new EmailOtpMail($otp));

        // Store session data
        Session::put('register_email', $data['email']);
        Session::put('register_name', $data['name']);
        Session::put('register_password', Hash::make($data['password']));
        Session::put('register_phone', $data['phone']);

        return [
            'status' => true,
            'message' => __('auth.otp_sent'),
            'data' => [
                'email' => $data['email'],
                'name' => $data['name'],
                'phone' => $data['phone'],
                'language' => $language,
            ]
        ];
    }
    public function verifyOtp(string $otp, string $language): array
    {
        $email = Session::get('register_email');
        $name = Session::get('register_name');
        $password = Session::get('register_password');
        $phone = Session::get('register_phone');

        if (!$email || !$name || !$password) {
            return [
                'status' => false,
                'message' => __('auth.session_expired'),
            ];
        }

        $emailOtp = EmailOtp::where('email', $email)
            ->where('otp', $otp)
            ->where('expired_at', '>=', now())
            ->first();

        if (!$emailOtp) {
            return [
                'status' => false,
                'message' => __('auth.invalid_otp'),
            ];
        }

        // إنشاء المستخدم
        $user = $this->userRepo->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'language' => $language,
            'phone' => $phone,
        ]);
        $user->refresh();

        $patient = null;
        $patient_record = null;

        if ($user->role === 'patient') {
            $patient = Patient::create([
                'user_id' => $user->id,
                'photo' => 'avatars/6681221.png',
            ]);

            $patient_record = Patient_record::create([
                'patient_id' => $patient->id,
            ]);
        }

        Auth::login($user);
        $token = $user->createToken('auth_token')->plainTextToken;

        // تنظيف الجلسة و OTP
        $emailOtp->delete();
        Session::forget([
            'register_email',
            'register_name',
            'register_password',
            'register_phone',
        ]);
        // تحميل العلاقات المطلوبة لعرضها في Resources
        $patient?->load('user');
        $patient_record?->load('patient');

        return [
            'status' => true,
            'message' => __('auth.register_success'),
            'user' => $user,
            'token' => $token,
            'patient' => $patient,
            'patient_record' => $patient_record,
        ];
    }
}

