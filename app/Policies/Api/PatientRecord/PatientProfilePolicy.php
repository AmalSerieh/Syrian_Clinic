<?php

namespace App\Policies\Api\PatientRecord;

use App\Models\Patient_profile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PatientProfilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Patient_profile $patientProfile): bool
    {

        // إذا كان المريض يملك هذا السجل
        if ($user->isPatient()) {
            return $user->patient
                && $user->patient->patient_record
                && $user->patient->patient_record->id === $patientProfile->patient_record_id
                && !$user->patient->patient_record->profile_submitted;
            ;
        }

        // الطبيب: فقط إذا كان لديه موعد مؤكد/منتهي مع المريض (مثال)
        if ($user->isDoctor()) {
            return $user->doctor
                && $patientProfile->patientRecord
                && $patientProfile->patientRecord->patient
                && $user->doctor->hasFinishedOrConfirmedAppointmentWith($patientProfile->patientRecord->patient->id);
            // ملاحظة: يجب أن تنشئ العلاقة hasFinishedOrConfirmedAppointmentWith() في Model Doctor ليتحقق مما إذا كان هناك موعد مع المريض
        }

        return false;

    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $hasProfile = $user->patient
            && $user->patient->patient_record
            && $user->patient->patient_record->patient_profile;

        if (!$hasProfile) {
            return true;
        }

        // المريض يمكنه الإنشاء فقط إذا لم يكن لديه ملف مريض
        if ($user->isPatient()) {
            return !$user->patient || !$user->patient->patient_record;
        }
        // المريض يمكنه الإنشاء مرة واحدة فقط
        if ($user->isPatient() && $user->patient && $user->patient->patient_record) {
            if (optional($user->patient->patient_record->patient_profile)->exists() === false) {
                return true;

            }

        }

        // الطبيب يمكنه فقط إذا أنشأت السكرتيرة الحساب
        if ($user->isDoctor() && $user->created_by_secretary) {
            return true;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Patient_profile $patient_profile): bool
    {
        return $user->isDoctor();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Patient_profile $patient_profile): bool
    {
        return $user->isDoctor();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Patient_profile $patient_profile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Patient_profile $patient_profile): bool
    {
        return false;
    }
}
