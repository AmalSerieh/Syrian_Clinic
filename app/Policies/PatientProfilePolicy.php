<?php

namespace App\Policies;

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
        // السماح للمريض برؤية ملفه
        if (
            $user->patient &&
            $user->patient->patient_record &&
            $user->patient->patient_record->patient_profile
        ) {
            return true;
        }

        // السماح للطبيب برؤية الملف الطبي لأي مريض
        if ($user->doctor) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->patient &&
            $user->patient->patient_record &&
            $user->patient->patient_record->patient_profile === null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Patient_profile $patientProfile): bool
    {
         // تحقق من أن المستخدم طبيب
    if (!$user->doctor) {
        return false;
    }

    // جلب المريض من الملف الطبي
    $patient = $patientProfile->patient_record->patient;

    // تحقق من وجود موعد سابق أو مؤكد بين الطبيب وهذا المريض
    return $user->doctor->appointments()
        ->where('patient_id', $patient->id)
        ->whereIn('status', ['confirmed', 'done']) // عدل الحالات حسب نظامك
        ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Patient_profile $patientProfile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Patient_profile $patientProfile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Patient_profile $patientProfile): bool
    {
        return false;
    }
}
