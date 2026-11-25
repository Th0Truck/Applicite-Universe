<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SaveGalleryRequest;
use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;

class DashboardGalleryController extends Controller
{
    /**
     * Display galleries in the dashboard.
     */
    public function index(): View
    {
        return view('dashboard.galleries.index', [
            'galleries' => Gallery::query()
                ->withCount('images')
                ->orderBy('title')
                ->paginate(20),
        ]);
    }

    /**
     * Show the gallery creation form.
     */
    public function create(): View
    {
        return view('dashboard.galleries.create', [
            'gallery' => new Gallery(['is_published' => true]),
        ]);
    }

    /**
     * Store a newly created gallery.
     */
    public function store(SaveGalleryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $gallery = Gallery::create($this->galleryAttributes($validated));

        return redirect()
            ->route('dashboard.galleries.edit', $gallery)
            ->with('status', 'Gallery created.');
    }

    /**
     * Show the gallery edit form.
     */
    public function edit(Gallery $gallery): View
    {
        $gallery->load('images');

        return view('dashboard.galleries.edit', [
            'gallery' => $gallery,
            'images' => $this->imageOptions(),
            'attachedImageIds' => $gallery->images->pluck('id'),
        ]);
    }

    /**
     * Update an existing gallery.
     */
    public function update(SaveGalleryRequest $request, Gallery $gallery): RedirectResponse
    {
        $validated = $request->validated();

        $gallery->update($this->galleryAttributes($validated));

        return redirect()
            ->route('dashboard.galleries.edit', $gallery)
            ->with('status', 'Gallery updated.');
    }

    /**
     * Delete a gallery.
     */
    public function destroy(Gallery $gallery): RedirectResponse
    {
        $gallery->delete();

        return redirect()
            ->route('dashboard.galleries.index')
            ->with('status', 'Gallery deleted.');
    }

    /**
     * Prepare gallery attributes for persistence.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function galleryAttributes(array $validated): array
    {
        return [
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'is_published' => (bool) ($validated['is_published'] ?? false),
        ];
    }

    /**
     * Get reusable images for gallery forms.
     *
     * @return Collection<int, GalleryImage>
     */
    private function imageOptions(): Collection
    {
        return GalleryImage::query()
            ->withCount('galleries')
            ->latest()
            ->get();
    }
}
