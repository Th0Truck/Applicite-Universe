<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Gallery;
use Illuminate\Support\Facades\DB;

/**
 * Replace gallery attachments without deleting shared library images.
 */
class SyncGalleryImages
{
    /**
     * Save a validated selection in its supplied order under the gallery write lock.
     *
     * @param array<int|string, int|string> $imageIds
     */
    public function execute(Gallery $gallery, array $imageIds): void
    {
        $payload = [];

        foreach (array_values($imageIds) as $index => $id) {
            $payload[$id] = ['sort_order' => $index];
        }

        DB::transaction(function () use ($gallery, $payload): void {
            Gallery::query()->whereKey($gallery->id)->lockForUpdate()->firstOrFail();
            $gallery->images()->sync($payload);
        });
    }
}
