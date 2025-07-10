<?php
namespace App\Repositories\Eloquent\Profile;

use App\Repositories\Profile\FileStorageInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
class FileStorageRepository implements FileStorageInterface
{
    public function storeAvatar($file): string
    {
        return $file->store('avatars', 'public');

    }
    public function getFullUrl(string $path): string
    {
        // استخدام URL::to() مع تضمين المنفذ
      //  return URL::to('storage/' . $path);
        return config('app.url') . '/storage/' . $path;
    }

    public function deleteOldAvatar(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }
}
