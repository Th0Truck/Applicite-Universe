<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CmsPageController extends Controller
{
    /**
     * Display CMS pages in the dashboard.
     */
    public function index(): View
    {
        return view('dashboard.cms.pages.index', [
            'pages' => CmsPage::query()
                ->withCount('paragraphs')
                ->latest()
                ->paginate(20),
        ]);
    }

    /**
     * Show the page creation form.
     */
    public function create(): View
    {
        return view('dashboard.cms.pages.create', [
            'page' => new CmsPage(['template' => 'standard', 'is_published' => true]),
            'paragraphs' => collect(),
            'templates' => config('cms.templates'),
        ]);
    }

    /**
     * Store a newly created CMS page.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedPage($request);
        $page = CmsPage::create($this->pageAttributes($validated));

        $this->syncParagraphs($request, $page);

        return redirect()
            ->route('dashboard.cms.pages.edit', $page)
            ->with('status', 'Page created.');
    }

    /**
     * Show the page edit form.
     */
    public function edit(CmsPage $page): View
    {
        return view('dashboard.cms.pages.edit', [
            'page' => $page->load('paragraphs'),
            'paragraphs' => $page->paragraphs,
            'templates' => config('cms.templates'),
        ]);
    }

    /**
     * Update a CMS page.
     */
    public function update(Request $request, CmsPage $page): RedirectResponse
    {
        $validated = $this->validatedPage($request, $page);

        $page->update($this->pageAttributes($validated));
        $this->syncParagraphs($request, $page);

        return redirect()
            ->route('dashboard.cms.pages.edit', $page)
            ->with('status', 'Page updated.');
    }

    /**
     * Delete a CMS page.
     */
    public function destroy(CmsPage $page): RedirectResponse
    {
        foreach ($page->paragraphs as $paragraph) {
            $this->deleteImage($paragraph->image_path);
        }

        $page->delete();

        return redirect()
            ->route('dashboard.cms.pages.index')
            ->with('status', 'Page deleted.');
    }

    /**
     * Display a published CMS page.
     */
    public function show(string $slug): View
    {
        $page = CmsPage::query()
            ->with('paragraphs')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $template = config("cms.templates.{$page->template}.view", 'cms.templates.standard');

        return view($template, [
            'page' => $page,
        ]);
    }

    /**
     * Validate page input.
     *
     * @return array<string, mixed>
     */
    private function validatedPage(Request $request, ?CmsPage $page = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('cms_pages', 'slug')->ignore($page?->id),
            ],
            'template' => ['required', Rule::in(array_keys(config('cms.templates')))],
            'is_published' => ['nullable', 'boolean'],
            'paragraphs' => ['array'],
            'paragraphs.*.id' => ['nullable', 'integer', 'exists:cms_paragraphs,id'],
            'paragraphs.*.heading' => ['nullable', 'string', 'max:255'],
            'paragraphs.*.subheading' => ['nullable', 'string', 'max:255'],
            'paragraphs.*.body' => ['nullable', 'string'],
            'paragraphs.*.image' => ['nullable', 'image', 'max:4096'],
            'paragraphs.*.remove_image' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * Prepare page attributes for persistence.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function pageAttributes(array $validated): array
    {
        $slug = trim((string) ($validated['slug'] ?? ''));

        return [
            'title' => $validated['title'],
            'slug' => Str::slug($slug !== '' ? $slug : $validated['title']),
            'template' => $validated['template'],
            'is_published' => (bool) ($validated['is_published'] ?? false),
        ];
    }

    /**
     * Synchronize paragraph blocks for a page.
     */
    private function syncParagraphs(Request $request, CmsPage $page): void
    {
        $existingParagraphs = $page->paragraphs()->get()->keyBy('id');
        $keptParagraphIds = [];

        foreach ($request->input('paragraphs', []) as $index => $paragraphInput) {
            $heading = trim((string) Arr::get($paragraphInput, 'heading', ''));
            $body = trim((string) Arr::get($paragraphInput, 'body', ''));

            if ($heading === '' && $body === '') {
                continue;
            }

            $paragraph = $existingParagraphs->get(Arr::get($paragraphInput, 'id')) ?? $page->paragraphs()->make();
            $imagePath = $paragraph->image_path;

            if ($request->boolean("paragraphs.{$index}.remove_image")) {
                $this->deleteImage($imagePath);
                $imagePath = null;
            }

            if ($request->hasFile("paragraphs.{$index}.image")) {
                $this->deleteImage($imagePath);
                $imagePath = $request->file("paragraphs.{$index}.image")->store('cms', 'public');
            }

            $paragraph->fill([
                'sort_order' => $index,
                'heading' => $heading,
                'subheading' => Arr::get($paragraphInput, 'subheading'),
                'body' => $body,
                'image_path' => $imagePath,
            ]);

            $paragraph->save();
            $keptParagraphIds[] = $paragraph->id;
        }

        $page->paragraphs()
            ->whereNotIn('id', $keptParagraphIds)
            ->get()
            ->each(function ($paragraph): void {
                $this->deleteImage($paragraph->image_path);
                $paragraph->delete();
            });
    }

    /**
     * Delete an uploaded paragraph image.
     */
    private function deleteImage(?string $imagePath): void
    {
        if ($imagePath !== null) {
            Storage::disk('public')->delete($imagePath);
        }
    }
}
