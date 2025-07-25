<?php

namespace App\Http\Requests\Api\Appointement;

use Illuminate\Foundation\Http\FormRequest;

class SetArrivvedTimeRequest extends FormRequest
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
            'arrivved_time' => 'required|integer|min:1'
        ];
    }
}
