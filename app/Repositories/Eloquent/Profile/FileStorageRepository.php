<?php
namespace App\Repositories\Eloquent\Profile;

use App\Repositories\Profile\FileStorageInterface;
use Illuminate\Support\Facades\Storage;
class FileStorageRepository implements FileStorageInterface
{
    public function storeAvatar($file): string
    {
        return $file->store('avatars', 'public');

    }

    public function deleteOldAvatar(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }
}
