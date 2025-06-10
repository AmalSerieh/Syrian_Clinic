<?php

namespace App\Policies;

use App\Models\Patient_record;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PatientRecordPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->patient !== null;
    }


    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Patient_record $patientRecord): bool
    { // المريض يشوف سجله فقط
        if ($user->patient && $user->patient->id === $patientRecord->patient_id) {
            return true;
        }
        // الطبيب يشوف كل السجلات
        /* if ($user->doctors) {
            return true;
        } */

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // المريض فقط ينشئ السجل بشرط ما يكون عنده سجل مسبقًا
          return $user->patient && $user->patient->patient_record === null;
        //return $user->patient !== null;
    }


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Patient_record $patientRecord): bool
    {
        // الطبيب فقط يمكنه التعديل
        return $user->doctor !== null;
        // return $user->is_doctor || $user->id === $patientRecord->patient->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Patient_record $patientRecord): bool
    {
        // الطبيب فقط يمكنه الحذف
        return $user->doctor !== null;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Patient_record $patientRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Patient_record $patientRecord): bool
    {
        return false;
    }
}
