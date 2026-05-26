<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class StorageHelper
{
    /**
     * Store an uploaded file and return the file path.
     *
     * @param UploadedFile $file The uploaded file.
     * @param string $folder The folder to store the file in.
     * @param string $disk The storage disk to use (default: 'public').
     * @return string The file path.
     *
     * @throws InvalidArgumentException If the folder is empty.
     */
    public static function storeFile(UploadedFile $file, string $folder, string $disk = 'public'): string
    {
        if (empty($folder)) {
            throw new InvalidArgumentException('Folder cannot be empty.');
        }

        return $file->store($folder, $disk);
    }

    /**
     * Delete an file from the storage.
     *
     * @param string $path The file path to delete.
     * @param string $disk The storage disk to use (default: 'public').
     * @return bool True if the file was deleted, false otherwise.
     */
    public static function deleteFile(string $path, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }
}
