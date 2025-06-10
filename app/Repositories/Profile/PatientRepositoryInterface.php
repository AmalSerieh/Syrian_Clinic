<?php
namespace App\Repositories\Profile;

use App\Models\Patient;
use App\Models\User;
interface PatientRepositoryInterface
{
    public function updateUserProfile(User $user, array $data): User;
    public function updatePatientProfile(Patient $patient, array $data): Patient;
    public function updateAvatar(Patient $patient, string $path): bool;
}
