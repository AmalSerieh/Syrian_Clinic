<?php

namespace App\Http\Requests\Api\PatientRecord;

use Illuminate\Foundation\Http\FormRequest;

class MedicalFileRequest extends FormRequest
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
            'test_name' => ['required', 'string'],
            'test_laboratory' => ['required', 'string'],
            'test_date' => ['required', 'date'],
            'test_image_pdf' => [
                'required',//nullable
                'file',
                function ($attribute, $value, $fail) {
                    if (request()->hasFile('test_image_pdf') && is_array(request()->file('test_image_pdf'))) {
                        $fail('يسمح برفع ملف واحد فقط.');
                    }
                },
                'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,pptx',
                'max:10240'
            ],
        ];
    }
    public function messages(): array
    {
        $locale = app()->getLocale();

        if ($locale === 'ar') {
            return [
                'test_name.required' => 'اسم التحليل مطلوب.',
                'test_name.string' => 'يجب أن يكون اسم التحليل نصًا صحيحًا.',

                'test_laboratory.required' => 'اسم المختبر مطلوب.',
                'test_laboratory.string' => 'يجب أن يكون اسم المختبر نصًا صحيحًا.',

                'test_date.required' => 'تاريخ التحليل مطلوب.',
                'test_date.date' => 'يجب إدخال تاريخ صحيح للتحليل.',

                'test_image_pdf.required' => 'يجب رفع ملف التحليل.',
                'test_image_pdf.file' => 'يجب أن يكون الملف صالحًا.',
                'test_image_pdf.mimes' => 'صيغة الملف غير مدعومة. الصيغ المسموحة: jpg, jpeg, png, webp, pdf, doc, docx, xls, xlsx, pptx.',
                'test_image_pdf.max' => 'يجب ألا يتجاوز حجم الملف 10 ميجابايت.',
            ];
        }
        return [
            'test_name.required' => 'Test name is required.',
            'test_name.string' => 'Test name must be a valid string.',
            'test_laboratory.required' => 'Test laboratory name is required.',
            'test_laboratory.string' => 'Test laboratory name must be a valid string.',
            'test_date.required' => 'Test date is required.',
            'test_date.date' => 'Test date must be a ',
            'test_image_pdf.required' => 'Test image pdf is required.',
            'test_image_pdf.file' => 'Test image pdf must be a file.',
            'test_image_pdf.mimes' => 'Test image pdf must be a valid file.',
            'test_image_pdf.max' => 'Test image pdf must be a valid
            file.',
        ];

    }
}
