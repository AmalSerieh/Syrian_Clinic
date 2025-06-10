<?php
namespace App\Repositories\Profile;
interface FileStorageInterface
{
    public function storeAvatar($file): string;
    public function deleteOldAvatar(string $path): bool;
}
