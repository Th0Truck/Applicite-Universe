<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Support\GalleryImageUploader;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DashboardGalleryController extends Controller
{
    public function __construct(private readonly GalleryImageUploader $uploader)
    {
    }

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
            'images' => $this->imageOptions(),
            'attachedImageIds' => collect(),
        ]);
    }

    /**
     * Store a newly created gallery.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedGallery($request);
        $gallery = Gallery::create($this->galleryAttributes($validated));

        $this->syncImages($request, $gallery, Arr::get($validated, 'image_ids', []));

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
        $this->syncImages($request, $gallery, Arr::get($validated, 'image_ids', []));

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
            'image_ids' => ['array'],
            'image_ids.*' => ['integer', 'exists:gallery_images,id'],
            'new_images' => ['array'],
            'new_images.*' => ['image', 'max:4096'],
            'new_image_alt_text' => ['nullable', 'string', 'max:255'],
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
     * Synchronize selected and newly uploaded images for a gallery.
     *
     * @param  array<int, int|string>  $selectedImageIds
     */
    private function syncImages(Request $request, Gallery $gallery, array $selectedImageIds): void
    {
        $imageIds = collect($selectedImageIds)
            ->map(fn (int|string $imageId): int => (int) $imageId)
            ->unique()
            ->values();

        foreach ($request->file('new_images', []) as $file) {
            $imageIds->push($this->uploader->upload($file, $request->input('new_image_alt_text'))->id);
        }

        $syncPayload = $imageIds
            ->unique()
            ->values()
            ->mapWithKeys(fn (int $imageId, int $index): array => [
                $imageId => ['sort_order' => $index],
            ])
            ->all();

        $gallery->images()->sync($syncPayload);
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
