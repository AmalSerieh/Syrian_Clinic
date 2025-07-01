<?php

namespace App\Http\Controllers\Web\Auth_Otp;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DashProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // معالجة الصورة إن وُجدت
    if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('doctor-profile-photos', 'public');

        // حذف القديمة إن وُجدت
        if ($user->role === 'doctor' && $user->doctor) {
            if ($user->doctor->photo && Storage::disk('public')->exists($user->doctor->photo)) {
                Storage::disk('public')->delete($user->doctor->photo);
            }
            $user->doctor->photo = $path;
            $user->doctor->save();
        } elseif ($user->role === 'secretary' && $user->secretary) {
            if ($user->secretary->photo && Storage::disk('public')->exists($user->secretary->photo)) {
                Storage::disk('public')->delete($user->secretary->photo);
            }
            $user->secretary->photo = $path;
            $user->secretary->save();
        }

        }
        return redirect()->back()->with('status','profile_update');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
