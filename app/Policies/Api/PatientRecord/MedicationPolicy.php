<?php

namespace App\Policies\Api\PatientRecord;

use App\Models\Medication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MedicationPolicy
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
    public function view(User $user, Medication $medication): bool
    {

        if (!$medication->exists) {
            return false;
        }
        // للمريض
        if ($user->isPatient()) {
            return $user->patient &&
                $user->patient->patient_record &&
                $user->patient->patient_record->id === $medication->patient_record_id
                && !$user->patient->patient_record->medications_submitted;
            ;
        }

        // للطبيب
        if ($user->isDoctor()) {
            if (!$medication->patientRecord || !$medication->patientRecord->patient) {
                return false;
            }

            return $user->doctor &&
                method_exists($user->doctor, 'hasFinishedOrConfirmedAppointmentWith') &&
                $user->doctor->hasFinishedOrConfirmedAppointmentWith($medication->patientRecord->patient->id);
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
    public function update(User $user, Medication $medication): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Medication $medication): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Medication $medication): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Medication $medication): bool
    {
        return false;
    }
}
