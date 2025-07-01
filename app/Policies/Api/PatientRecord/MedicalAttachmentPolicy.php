<?php

namespace App\Policies\Api\PatientRecord;

use App\Models\MedicalAttachment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MedicalAttachmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isPatient() || $user->isDoctor();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MedicalAttachment $medicalAttachment): bool
    {
        // إذا كان المريض يملك هذا السجل
        if ($user->isPatient()) {
            return $user->patient
                && $user->patient->patient_record
                && $user->patient->patient_record->id === $medicalAttachment->patient_record_id
                && !$user->patient->patient_record->medicalAttachments_submitted;
            ;
        }

        // الطبيب: فقط إذا كان لديه موعد مؤكد/منتهي مع المريض (مثال)
        if ($user->isDoctor()) {
            return $user->doctor
                && $medicalAttachment->patientRecord
                && $medicalAttachment->patientRecord->patient
                && $user->doctor->hasFinishedOrConfirmedAppointmentWith($medicalAttachment->patientRecord->patient->id);
            // ملاحظة: يجب أن تنشئ العلاقة hasFinishedOrConfirmedAppointmentWith() في Model Doctor ليتحقق مما إذا كان هناك موعد مع المريض
        }

        return false;

    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // 🔒 المريض فقط إذا عنده سجل طبي
        if ($user->isPatient()) {
            return $user->patient
                && $user->patient->patient_record;
        }

        // 🔐 الطبيب فقط إذا الحساب منشأ من سكرتيرة
        if ($user->isDoctor()) {
            return $user->created_by_secretary === true;
        }


        // المريض يمكنه الإنشاء مرة واحدة فقط
        if ($user->isPatient() && $user->patient && $user->patient->patient_record) {
            if (optional($user->patient->patient_record->medicalAttachment)->exists() === false) {
                return true;

            }

        }

        // الطبيب يمكنه فقط إذا أنشأت السكرتيرة الحساب
        if ($user->isDoctor() && $user->created_by_secretary) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicalAttachment $medicalAttachment): bool
    {
        return $user->isDoctor();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicalAttachment $medicalAttachment): bool
    {
        return $user->isDoctor();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicalAttachment $medicalAttachment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicalAttachment $medicalAttachment): bool
    {
        return false;
    }
}
