<?php

namespace App\Http\Controllers\Auth_Otp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class ResetPasswordController extends Controller
{
    public function changePassword(ResetPasswordRequest $request)
    {
        $language = $this->parseLanguageHeader($request->header('Accept-Language'));
        // dd($language);
        app()->setLocale($language);
        //  dd( app()->setLocale($language));
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => __('auth.current_password_incorrect'),
            ], 403);
        }

        $user->password = bcrypt($request->new_password);
        $user->language = $language;
        $user->save();

        return response()->json([
            'message' => __('auth.password_changed_successfully'),
        ]);
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
}
