<?php

namespace App\Policies;

use App\Models\Operation;
use App\Models\Patient_record;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OperationsPolicy
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
    public function view(User $user, Operation $operation): bool
    {
        return $user->doctor || (
            $user->patient && $operation->patient_record_id === $user->patient->patient_record->id
        );
    }

    /**
     * Determine whether the user can create models.
     */
   public function create(User $user, Patient_record $record): bool
    {
        // الطبيب يمكنه الإضافة دائمًا
        if ($user->doctor) {
            return true;
        }

        // المريض يمكنه الإضافة فقط إذا لم يكن قد أرسل البيانات سابقًا
        if ($user->patient) {
        return !$record->operations_submitted;
    }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Operation $operation): bool
    {

        // تحقق من أن المستخدم طبيب
        if (!$user->doctor) {
            return false;
        }

        // جلب المريض من الملف الطبي
        $patient = $operation->patient_record->patient;

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
    public function delete(User $user, Operation $operation): bool
    {
        return !!$user->doctor;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Operation $operation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Operation $operation): bool
    {
        return false;
    }
}
