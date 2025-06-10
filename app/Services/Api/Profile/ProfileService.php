<?php
namespace App\Services\Api\Profile;

use App\Exceptions\FileUploadException;
use App\Exceptions\ProfileUpdateException;
use App\Models\Patient;
use App\Models\User;
use App\Repositories\Profile\FileStorageInterface;
use App\Repositories\Profile\PatientRepositoryInterface;
use Throwable;

class ProfileService
{
    public function __construct(
        private PatientRepositoryInterface $repository,
        private FileStorageInterface $fileStorage
    ) {
    }



    public function updateProfile(User $user, array $data): array
    {
        $changes = [];
        $userData = [];
        $patientData = [];

        if (isset($data['name']) && $user->name !== $data['name']) {
            $userData['name'] = $data['name'];
            $changes['name'] = $data['name'];
        }

        if (isset($data['email']) && $user->email !== $data['email']) {
            $userData['email'] = $data['email'];
            $changes['email'] = $data['email'];
        }
        if (isset($data['phone']) && $user->phone !== $data['phone']) {
            $userData['phone'] = $data['phone'];
            $changes['phone'] = $data['phone'];
        }

        if (!empty($userData)) {
            $user = $this->repository->updateUserProfile($user, $userData);
        }

        if (isset($data['photo'])) {
            if ($user->patient->photo) {
                $this->fileStorage->deleteOldAvatar($user->patient->photo);
            }
            $path = $this->fileStorage->storeAvatar($data['photo']);
            $patient = $this->repository->updatePatientProfile($user->patient, ['photo' => $path]);
            $changes['photo'] = asset('storage/' . $path);
        }
        return [
            'user' => $user,
            'changes' => $changes
        ];
    }

    public function updateAvatar(Patient $patient, $image): string
    {
        try {
            if ($patient->photo) {
                $this->fileStorage->deleteOldAvatar($patient->photo);
            }

            return $this->fileStorage->storeAvatar($image);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تحميل الملف',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
