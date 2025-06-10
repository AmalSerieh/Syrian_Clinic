<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', 'min:8', Password::defaults()],
            'phone'=>['required','digits:10','numeric'],
        ];
    }
    public function messages3(): array
    {
        return [
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.confirmed' => 'تأكيد كلمة المرور غير مطابق.',
            'password.min' => 'كلمة المرور يجب أن تكون على الأقل 8 أحرف.',
            'password.letters' => 'كلمة المرور يجب أن تحتوي على حروف.',
            'password.mixed' => 'كلمة المرور يجب أن تحتوي على حروف كبيرة وصغيرة.',
            'password.numbers' => 'كلمة المرور يجب أن تحتوي على أرقام.',
            'password.symbols' => 'كلمة المرور يجب أن تحتوي على رموز.',
            'password.uncompromised' => 'كلمة المرور هذه تم تسريبها من قبل. الرجاء اختيار كلمة أخرى.',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('auth.name')]),
            'email.required' => __('validation.required', ['attribute' => __('auth.email')]),
            'email.unique' => __('auth.email_used'),
            'password.required' => __('validation.required', ['attribute' => __('auth.password')]),
            'password.confirmed' => __('validation.confirmed', ['attribute' => __('auth.password')]),
            'password.min' => __('validation.min.string', ['attribute' => __('auth.password'), 'min' => 8]),
        ];
    }

}
