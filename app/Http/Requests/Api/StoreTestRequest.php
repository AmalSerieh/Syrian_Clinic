<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestRequest extends FormRequest
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
            'test_name' => 'required|string|max:255',
            'test_result' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (request()->hasFile('test_result')) {
                        $file = request()->file('test_result');
                        $allowedMimeTypes = [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                            'image/webp', // صور
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // DOC, DOCX
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // XLS, XLSX
                        ];

                        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                            $fail(__('message.test_result_invalid_file')); // الترجمة
                        }
                    } elseif (!is_string($value)) {
                        $fail(__('message.test_result_invalid_text')); // الترجمة
                    }
                },
            ],


            'test_date' => 'required|date',
        ];
    }
}
