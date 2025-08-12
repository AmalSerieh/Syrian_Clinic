<?php

namespace App\Http\Controllers\API\PatientRecord;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PatientRecord\MedicalAttachmentRequest;
use App\Http\Resources\Api\PateintRecord\MedicalAttachmentResource;
use App\Models\MedicalAttachment;
use App\Services\Api\PateintRecord\MedicalAttachmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalAttachmentController extends Controller
{
    public function __construct(protected MedicalAttachmentService $service)
    {
    }
    public function store(MedicalAttachmentRequest $request)
    {
        $user = Auth::user();
        $recordId = $user->patient->patient_record->id;
        if (!$recordId) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }
        if ($user->patient->patient_record->medicalAttachments_submitted) {
            return response()->json(['message' => trans('message.submitted_already')], 403);
        }


        $this->authorize('create', MedicalAttachment::class);

        $attachment = $this->service->store($request->validated(), $recordId);

        return response()->json([
            'message' =>  trans('message.MedicalAttachment_saved'),
            'data' => new MedicalAttachmentResource($attachment)
        ], 200);
    }
    public function index()
    {
        $user = Auth::user();
        $recordId = $user->patient?->patient_record?->id;

        if (!$recordId) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }
        /*  if ($user->patient->patient_record->medicalAttachments_submitted) {
            return response()->json(['message' => trans('message.submitted_already')], 403);
        } */
        $this->authorize('viewAny', MedicalAttachment::class);

        $attachments = $this->service->getByPatientRecord($recordId);

        return MedicalAttachmentResource::collection($attachments);
    }
    public function update(MedicalAttachmentRequest $request, int $id)
    {
        $this->authorize('update', MedicalAttachment::findOrFail($id));

        $attachment = $this->service->update($id, $request->validated());

        return response()->json([
            'message' => 'تم تحديث الفحص بنجاح',
            'data' => new MedicalAttachmentResource($attachment)
        ]);
    }
    public function destroy(int $id)
    {
        $this->authorize('delete', MedicalAttachment::findOrFail($id));

        $this->service->delete($id);

        return response()->json([
            'message' => 'تم حذف الفحص بنجاح'
        ]);
    }


}
