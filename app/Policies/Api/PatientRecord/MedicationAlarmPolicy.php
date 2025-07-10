<?php

namespace App\Policies\Api\PatientRecord;

use App\Models\MedicationAlarm;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MedicationAlarmPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isPatient();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MedicationAlarm $medicationAlarm): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
         if ($user->isPatient()) {
            return $user->patient
                && $user->patient->patient_record->medications;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicationAlarm $medicationAlarm): bool
    {
        return $medicationAlarm->medication->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicationAlarm $medicationAlarm): bool
    {
         return $medicationAlarm->medication->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicationAlarm $medicationAlarm): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicationAlarm $medicationAlarm): bool
    {
        return false;
    }
}
