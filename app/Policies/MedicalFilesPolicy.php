<?php

namespace App\Policies;

use App\Models\MedicalFile;
use App\Models\Patient_record;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MedicalFilesPolicy
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
    public function view(User $user, MedicalFile $medicalFile): bool
    {
        return $user->doctor || (
            $user->patient && $medicalFile->patient_record_id === $user->patient->patient_record->id
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Patient_record $record): bool
    {
        // المريض يمكنه رفع الملفات مرة واحدة فقط
        if ($user->patient) {
            return !$record->medicalfiles_submitted;
        }

        // الطبيب يمكنه رفع الملفات في أي وقت إذا كان له موعد مؤكد
        if ($user->doctor) {
            return $user->doctor->appointments()
                ->where('patient_id', $record->patient_id)
                ->whereIn('status', ['confirmed', 'done'])
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicalFile $medicalFile): bool
    {
        // تحقق من أن المستخدم طبيب
        if (!$user->doctor) {
            return false;
        }
        // جلب المريض من الملف الطبي
        $patient = $medicalFile->patient_record->patient;

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
    public function delete(User $user, MedicalFile $medicalFile): bool
    {
         return $user->doctor !== null;
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
