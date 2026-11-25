<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

/**
 * Store gallery uploads and clean up new files when persistence fails.
 */
class GalleryImageUploader
{
    /**
     * Upload a batch and append it to an existing gallery atomically.
     *
     * @param array<int|string, UploadedFile> $files
     */
    public function uploadToGallery(Gallery $gallery, array $files, ?string $altText = null): void
    {
        $paths = [];

        try {
            DB::transaction(function () use ($gallery, $files, $altText, &$paths): void {
                Gallery::query()->whereKey($gallery->id)->lockForUpdate()->firstOrFail();
                $sortOrder = (int) $gallery->images()->max('gallery_gallery_image.sort_order');

                foreach ($files as $file) {
                    $path = $file->store('galleries', 'public');

                    if ($path === false) {
                        throw new RuntimeException('Unable to store the gallery image.');
                    }

                    $paths[] = $path;
                    $image = GalleryImage::create([
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'alt_text' => $altText,
                    ]);

                    $gallery->images()->attach($image, ['sort_order' => ++$sortOrder]);
                }
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($paths);

            throw $exception;
        }
    }
}
