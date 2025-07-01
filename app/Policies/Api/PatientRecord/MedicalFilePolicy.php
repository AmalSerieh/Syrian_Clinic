<?php

namespace App\Policies\Api\PatientRecord;

use App\Models\MedicalFile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MedicalFilePolicy
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
    public function view(User $user, MedicalFile $medicalFile): bool
    {
        // تحقق من أن سجل الحساسية موجود
        if (!$medicalFile->exists) {
            return false;
        }

        // للمريض
        if ($user->isPatient()) {
            return $user->patient &&
                $user->patient->patient_record &&
                $user->patient->patient_record->id === $medicalFile->patient_record_id
                && !$user->patient->patient_record->medicalfiles_submitted;
            ;
        }

        // للطبيب
        if ($user->isDoctor()) {
            if (!$medicalFile->patientRecord || !$medicalFile->patientRecord->patient) {
                return false;
            }

            return $user->doctor &&
                method_exists($user->doctor, 'hasFinishedOrConfirmedAppointmentWith') &&
                $user->doctor->hasFinishedOrConfirmedAppointmentWith($medicalFile->patientRecord->patient->id);
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

        // 🔒 أي دور آخر مرفوض
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicalFile $medicalFile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicalFile $medicalFile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicalFile $medicalFile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicalFile $medicalFile): bool
    {
        return false;
    }
}
