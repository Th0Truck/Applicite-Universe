<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Support\GalleryImageUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Manage images independently of gallery details.
 */
class DashboardGalleryImageController extends Controller
{
    /**
     * Upload and attach images without changing existing gallery content.
     */
    public function store(Request $request, Gallery $gallery, GalleryImageUploader $uploader): RedirectResponse
    {
        $validated = $request->validate([
            'new_images' => ['required', 'array', 'min:1'],
            'new_images.*' => ['required', 'image', 'max:4096'],
            'new_image_alt_text' => ['nullable', 'string', 'max:255'],
        ], [
            'new_images.*.uploaded' => 'Image :position could not be uploaded. '
                . 'The server upload limit is ' . ini_get('upload_max_filesize')
                . ' per file. Choose a smaller image or contact the administrator.',
            'new_images.*.max' => 'Image :position must not exceed 4 MB.',
        ]);

        $uploader->uploadToGallery($gallery, $validated['new_images'], $validated['new_image_alt_text'] ?? null);

        return redirect()->route('dashboard.galleries.edit', $gallery)
            ->with('status', 'Images uploaded and attached.');
    }

    /**
     * Save the selected library images, including an intentionally empty selection.
     */
    public function update(Request $request, Gallery $gallery): RedirectResponse
    {
        $validated = $request->validate([
            'image_selection' => ['required', 'accepted'],
            'image_ids' => ['sometimes', 'array'],
            'image_ids.*' => ['required', 'integer', 'distinct', 'exists:gallery_images,id'],
        ]);

        $payload = [];

        foreach (array_values($validated['image_ids'] ?? []) as $index => $id) {
            $payload[$id] = ['sort_order' => $index];
        }

        DB::transaction(function () use ($gallery, $payload): void {
            Gallery::query()->whereKey($gallery->id)->lockForUpdate()->firstOrFail();
            $gallery->images()->sync($payload);
        });

        return redirect()->route('dashboard.galleries.edit', $gallery)
            ->with('status', 'Attached images updated.');
    }
}
