<?php
namespace App\Repositories\Eloquent\Profile;

use App\Models\Patient;
use App\Models\User;
use App\Repositories\Profile\PatientRepositoryInterface;
class PatientRepository implements PatientRepositoryInterface
{
    public function updateUserProfile(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }
    public function updatePatientProfile(Patient $patient, array $data): Patient
    {
        $patient->update($data);
        return $patient->fresh();
    }

    public function updateAvatar(Patient $patient, string $path): bool
    {
        return $patient->update(['photo' => $path]);
    }
}
