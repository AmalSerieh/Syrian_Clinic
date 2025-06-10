<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMedicalFileRequest;
use App\Http\Resources\MedicalFilesResource;
use App\Models\MedicalFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalFileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $record = $user->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        $this->authorize('viewAny', [MedicalFile::class, $record]);

        $medicalFiles = $record->medicalFiles;

        if ($medicalFiles->isEmpty()) {
            return response()->json(['message' => trans('message.not_filled_yet')]);
        }

        return MedicalFilesResource::collection($medicalFiles);
    }

    public function show(MedicalFile $medicalFile)
    {
        $this->authorize('view', $medicalFile);
        return new MedicalFilesResource($medicalFile);
    }
    public function store(StoreMedicalFileRequest $request)
    {
        $user = auth()->user();
        $record = $user->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        if ($record->medicalfiles_submitted) {
            return response()->json(['message' => trans('message.submitted_already')], 403);
        }

        $this->authorize('create', [MedicalFile::class, $record]);

        $paths = [];

        // تخزين الملفات المرفوعة في مجلد خاص بالسجل
        if ($request->hasFile('file_image_paths')) {
            foreach ($request->file('file_image_paths') as $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $paths[] = $file->store("medical_files/record_{$record->id}", 'public');
                }
            }
        }

        // إدخال روابط صور مباشرة (اختياري)
        $inputPaths = $request->input('file_image_paths', []);
        if (is_array($inputPaths)) {
            foreach ($inputPaths as $item) {
                if (is_string($item)) {
                    $paths[] = $item;
                }
            }
        }

        $medicalFile = $record->medicalFiles()->create([
            'file_image_paths' => $paths,
        ]);

        return new MedicalFilesResource($medicalFile);
    }


    //✅ تأكيد الإرسال
    public function submit()
    {
        $user = auth()->user();
        $record = $user->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        $this->authorize('confirmSubmission', [MedicalFile::class, $record]);

        // التحقق من وجود حساسيات
        if ($record->medicalFiles->isEmpty()) {
            return response()->json(['message' => trans('message.no_medicalFiles_found')], 404);
        }

        if ($record->medicalfiles_submitted) {
            return response()->json(['message' => trans('message.medicalFile_already_submitted')], 400);
        }

        $record->update(['medicalfiles_submitted' => 1]);

        if ($record->medicalfiles_submitted == 1) {
            return response()->json(['message' => trans('message.medicalFile_submitted_success')]);
        } else {
            return response()->json(['message' => trans('message.medicalFile_submitted_failed')], 500);
        }
    }

    // ✅ تعديل حساسية للطبيب

   public function update(StoreMedicalFileRequest $request, MedicalFile $medicalFile)
{
    $user = auth()->user();
    $record = $user->patient->patient_record;

    if (!$record || $medicalFile->patient_record_id !== $record->id) {
        return response()->json(['message' => trans('message.unauthorized')], 403);
    }

    if ($record->medicalfiles_submitted) {
        return response()->json(['message' => trans('message.submitted_already')], 403);
    }

    $this->authorize('update', $medicalFile);

    $paths = [];

    // رفع الملفات الجديدة
    if ($request->hasFile('file_image_paths')) {
        foreach ($request->file('file_image_paths') as $file) {
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                $paths[] = $file->storeAs(
                    'medical_files/record_' . $record->id,
                    $fileName,
                    'public'
                );
            }
        }
    }

    // روابط الصور (string)
    $inputPaths = $request->input('file_image_paths', []);
    if (is_array($inputPaths)) {
        foreach ($inputPaths as $item) {
            if (is_string($item) && preg_match('/^https?:\/\//', $item)) {
                $paths[] = $item;
            }
        }
    }

    // تحديث السجل بالملفات الجديدة (يمكن تعديل هذه الجزئية للدمج بدل الاستبدال)
    $medicalFile->update([
        'file_image_paths' => $paths,
    ]);

    return new MedicalFilesResource($medicalFile);
}




    // ✅ حذف حساسية للطبيب
    public function destroy(MedicalFile $medicalFile)
    {
        $this->authorize('delete', $medicalFile);
        $medicalFile->delete();

        return response()->json(['message' => trans('message.medicalFile_deleted')]);
    }

}
