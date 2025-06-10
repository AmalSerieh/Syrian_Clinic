<?php

namespace App\Http\Controllers\API\Auth\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\UpdateProfileRequest;
use App\Http\Resources\Api\Profile\ProfileResource;
use App\Services\Api\Profile\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class ProfileController extends Controller
{
    /* public function __construct(private ProfileService $profileService)
    {
    }


    public function update(UpdateProfileRequest $request)
    {
        try {
            $user = $request->user();
            $data = $request->validated();

            if ($request->hasFile('photo')) {
                $data['photo'] = $this->profileService->updateAvatar(
                    $user->patient,
                    $request->file('photo')
                );
            }

            $result = $this->profileService->updateFullProfile($user, $data);


            if (!$result['changed']) {
                return response()->json([
                    'message' => 'لم يتم تعديل أي بيانات.'
                ], 200);
            }
            return new ProfileResource([
                'user' => $result['user'],
                'patient' => $result['patient']
            ]);

        } catch (\Throwable $e) {


            return response()->json([
                'message' => 'حدث خطأ غير متوقع أثناء تحديث الملف الشخصي. الرجاء المحاولة لاحقاً.'
            ], 500);
        }
    } */


    public function show()
    {
        $user = Auth::user();
        $patient = $user->patient;

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'photo' => $patient?->photo ? asset('storage/' . $patient->photo) : null,
        ]);
    }

    public function update1(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $changes = [];

        // تحقق من الحقول المرسلة فقط
        if ($request->has('name') && $user->name !== $request->name) {
            $user->name = $request->name;
            $changes['name'] = $request->name;
        }

        if ($request->has('email') && $user->email !== $request->email) {
            $user->email = $request->email;
            $changes['email'] = $request->email;
        }

        if ($request->has('phone') && $user->phone !== $request->phone) {
            $user->phone = $request->phone;
            $changes['phone'] = $request->phone;
        }

        $user->save();

        // التعامل مع الصورة
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('avatars', 'public');

            $user->patient()->updateOrCreate(
                ['user_id' => $user->id],
                ['photo' => $path]
            );

            $changes['photo'] = asset('storage/' . $path);
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'updated_fields' => $changes,
        ]);
    }
    public function __construct(private ProfileService $profileService)
    {
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');
        }

        $result = $this->profileService->updateProfile($user, $data);
        return new ProfileResource([
            'message' => trans('message.update_profile'),
            'user' => $result['user'],
            'changes' => $result['changes']
        ]);
        /* return response()->json([
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'updated_fields' => $result['changes']
        ]); */
    }


}
