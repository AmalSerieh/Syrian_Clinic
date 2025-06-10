<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
         return [
        'file_image_paths' => 'required','array',
        'file_image_paths.*' => [
            'file',
            'nullable',
            'mimes:jpeg,jpg,png,webp,pdf,doc,docx,xls,xlsx,pptx',
            'max:10240', // 10MB كحد أقصى
        ],
    ];
    }

    public function messages(): array
    {
        return [
            'file_image_paths.required' => __('validation.file_required'),
            'file_image_paths.array' => __('validation.file_must_be_array'),
            'file_image_paths.*.file' => __('validation.file_invalid'),
            'file_image_paths.*.mimes' => __('validation.file_invalid_type'),
            'file_image_paths.*.max' => __('validation.file_too_large'),
        ];
    }
}
