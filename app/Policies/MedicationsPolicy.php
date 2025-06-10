<?php

namespace App\Policies;

use App\Models\Medication;
use App\Models\Patient_record;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MedicationsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Patient_record $record): bool
    {
        return $user->doctor || $user->patient !== null;

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Medication $medication): bool
    {
        return $user->doctor || (
            $user->patient && $medication->patient_record_id === $user->patient->patient_record->id
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // الطبيب يمكنه الإضافة دائمًا
        if ($user->doctor) {
            return true;
        }

        // المريض يمكنه الإضافة فقط إذا لم يكن قد أرسل البيانات سابقًا
        if ($user->patient) {
            $record = $user->patient->patient_record;
            return !$record->medications_submitted;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Medication $medication): bool
    {

        // تحقق من أن المستخدم طبيب
        if (!$user->doctor) {
            return false;
        }

        // جلب المريض من الملف الطبي
        $patient = $medication->patient_record->patient;

        // تحقق من وجود موعد سابق أو مؤكد بين الطبيب وهذا المريض
        return $user->doctor->appointments()
            ->where('patient_id', $patient->id)
            ->whereIn('status', ['confirmed', 'done']) // عدل الحالات حسب نظامك
            ->exists();
    }
    public function confirmSubmission(User $user, Patient_record $record): bool
    {
        return $user->patient && $user->patient->patient_record->id === $record->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Medication $medication): bool
    {
        return !!$user->doctor;
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
