<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedGallery($request);
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
    public function update(Request $request, Gallery $gallery): RedirectResponse
    {
        $validated = $this->validatedGallery($request, $gallery);

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
     * Validate gallery input.
     *
     * @return array<string, mixed>
     */
    private function validatedGallery(Request $request, ?Gallery $gallery = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('galleries', 'slug')->ignore($gallery?->id),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_published' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * Prepare gallery attributes for persistence.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function galleryAttributes(array $validated): array
    {
        $slug = trim((string) ($validated['slug'] ?? ''));

        return [
            'title' => $validated['title'],
            'slug' => Str::slug($slug !== '' ? $slug : $validated['title']),
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
