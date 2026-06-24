<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

trait HandlesImageUploads
{
    /**
     * Process an array of uploaded images into multiple scale variations.
     *
     * @param array<UploadedFile> $files
     * @param string $basePath
     * @param array<string, int> $dimensions [folder_suffix => width]
     * @return array<string> Array of generated filenames
     */
    protected function uploadAndResizeImages(array $files, string $basePath = 'products', array $dimensions = []): array
    {
        $storedFilenames = [];
        $manager = new ImageManager(new Driver());

        if (empty($dimensions)) {
            $dimensions = [
                'small_image/'  => 150,
                'medium_image/' => 600,
                'large_image/'  => 1200,
                ''              => 1200,
            ];
        }

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile || !$file->isValid()) {
                continue;
            }

            $filename = time() . '_' . uniqid() . '.webp';

            foreach ($dimensions as $folder => $width) {
                $imageInstance = $manager->read($file->getRealPath())->scale(width: $width);
                $targetPath = rtrim($basePath, '/') . '/' . $folder . $filename;
                
                Storage::disk('public')->put($targetPath, (string) $imageInstance->toWebp(quality: 80));
            }

            $storedFilenames[] = $filename;
        }

        return $storedFilenames;
    }

    /**
     * Purge image variations safely from storage. Supports string arrays or Object collections.
     */
    protected function deleteImageVariations(mixed $filenames, string $basePath = 'products', array $folders = ['small_image/', 'medium_image/', 'large_image/', '']): void
    {
        // If passed an Eloquent collection of objects, convert it to an array of string filenames
        if (is_object($filenames) && method_exists($filenames, 'pluck')) {
            $filenames = $filenames->pluck('image_url')->toArray();
        } elseif (is_object($filenames)) {
            $filenames = (array) $filenames;
        }

        foreach ((array) $filenames as $item) {
            // Extract filename if an individual object properties leak through
            $filename = is_object($item) ? ($item->image_url ?? null) : $item;

            if (empty($filename) || !is_string($filename)) {
                continue;
            }

            $pathsToDelete = array_map(function ($folder) use ($basePath, $filename) {
                return rtrim($basePath, '/') . '/' . $folder . $filename;
            }, $folders);

            Storage::disk('public')->delete($pathsToDelete);
        }
    }
}