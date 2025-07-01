<?php

namespace App\Http\Controllers\API\PatientRecord;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PatientRecord\MedicalFileRequest;
use App\Http\Resources\Api\PateintRecord\MedicalFileResource;
use App\Models\MedicalFile;
use App\Services\Api\PateintRecord\MedicalFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalFileController extends Controller
{
    public function __construct(protected MedicalFileService $service)
    {
    }
    public function store(MedicalFileRequest $request)
    {
        $user = Auth::user();
        $record = $user->patient?->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }
if ($record->medicalfiles_submitted) {
            return response()->json(['message' => trans('message.submitted_already')], 403);
        }
        $this->authorize('create', MedicalFile::class);

        $file = $this->service->store($request->validated(), $record->id);

        return response()->json([
            'message' => trans('message.medicalFile_saved'),
            'data' => new MedicalFileResource($file)
        ]);
    }
    public function index()
    {
        $user = Auth::user();
        $record = $user->patient?->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        $this->authorize('viewAny', MedicalFile::class);
        $medicalFiles = $user->patient->patient_record->medicalFiles;

        if ($medicalFiles->isEmpty()) {
            return response()->json(['message' => trans('message.no_data')], 404);
        }
        $files = $this->service->getAllForPatient($record->id);

        return MedicalFileResource::collection($files);
    }
    public function indexGrouped()
    {
        $user = Auth::user();
        $record = $user->patient?->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        $grouped = $this->service->getGroupedFilesByTypeAndTestName($record->id);

        return response()->json([
            'message' => trans('message.files_retrieved_successfully'),
            'data' => [
                'images' => collect($grouped['images'])->map(fn($items) => MedicalFileResource::collection($items)),
                //'images' => collect($grouped['images'])->map(fn($items) => MedicalFileResource::collection(collect($items)->mapInto(MedicalFile::class))),
                //'documents' => collect($grouped['documents'])->map(fn($items) => MedicalFileResource::collection(collect($items)->mapInto(MedicalFile::class))),

                'documents' => collect($grouped['documents'])->map(fn($items) => MedicalFileResource::collection($items)),
            ],
        ]);
    }// في MedicalFileController.php

    // لعرض الصور فقط
    public function getImages()
    {
        $user = Auth::user();
        $record = $user->patient?->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        $images = $this->service->getFilesByType($record->id, 'images');

        if ($images->isEmpty()) {
            return response()->json([
                'message' => trans('message.no_images_found'),
                'data' => []
            ], 404);
        }
        return response()->json([
            'message' => trans('message.images_retrieved_successfully'),
            'data' => collect($images)->groupBy('test_name')
                ->map(fn($items) => MedicalFileResource::collection($items))
        ]);
    }

    // لعرض الوثائق فقط
    public function getDocuments()
    {
        $user = Auth::user();
        $record = $user->patient?->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        $documents = $this->service->getFilesByType($record->id, 'documents');

        if ($documents->isEmpty()) {
            return response()->json([
                'message' => trans('message.no_documents_found'),
                'data' => []
            ], 404);
        }
        return response()->json([
            'message' => trans('message.documents_retrieved_successfully'),
            'data' => collect($documents)->groupBy('test_name')
                ->map(fn($items) => MedicalFileResource::collection($items))
        ]);
    }

}
