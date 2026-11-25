<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\SyncGalleryImages;
use App\Http\Requests\SyncGalleryImagesRequest;
use App\Http\Requests\UploadGalleryImagesRequest;
use App\Models\Gallery;
use App\Support\GalleryImageUploader;
use Illuminate\Http\RedirectResponse;

/**
 * Manage images independently of gallery details.
 */
class DashboardGalleryImageController extends Controller
{
    /**
     * Upload and attach images without changing existing gallery content.
     */
    public function store(
        UploadGalleryImagesRequest $request,
        Gallery $gallery,
        GalleryImageUploader $uploader
    ): RedirectResponse {
        $validated = $request->validated();

        $uploader->uploadToGallery($gallery, $validated['new_images'], $validated['new_image_alt_text'] ?? null);

        return redirect()->route('dashboard.galleries.edit', $gallery)
            ->with('status', 'Images uploaded and attached.');
    }

    /**
     * Save the selected library images, including an intentionally empty selection.
     */
    public function update(
        SyncGalleryImagesRequest $request,
        Gallery $gallery,
        SyncGalleryImages $syncImages
    ): RedirectResponse {
        $validated = $request->validated();
        $syncImages->execute($gallery, $validated['image_ids'] ?? []);

        return redirect()->route('dashboard.galleries.edit', $gallery)
            ->with('status', 'Attached images updated.');
    }
}
