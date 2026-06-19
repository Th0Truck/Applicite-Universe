<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Support\CmsHtmlSanitizer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
                ->with('parent')
                ->orderBy('parent_id')
                ->orderBy('sort_order')
                ->orderBy('title')
                ->paginate(20),
        ]);
    }

    /**
     * Show the page creation form.
     */
    public function create(): View
    {
        $parentId = request()->integer('parent_id') ?: null;

        return view('dashboard.cms.pages.create', [
            'page' => new CmsPage([
                'parent_id' => $parentId,
                'sort_order' => $this->nextSortOrder($parentId),
                'template' => 'standard',
                'is_published' => true,
            ]),
            'paragraphs' => collect(),
            'parentPages' => $this->parentPageOptions(),
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
            'parentPages' => $this->parentPageOptions($page),
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
            ->with(['paragraphs', 'parent'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $template = config("cms.templates.{$page->template}.view", 'cms.templates.standard');
        $topLevelPage = $page->parent ?? $page;

        return view($template, [
            'page' => $page,
            'topLevelPage' => $topLevelPage,
            'footerSubPages' => $this->publishedSubPages($topLevelPage),
        ]);
    }

    /**
     * Validate page input.
     *
     * @return array<string, mixed>
     */
    private function validatedPage(Request $request, ?CmsPage $page = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:cms_pages,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
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
            'paragraphs.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'paragraphs.*.heading' => ['nullable', 'string', 'max:255'],
            'paragraphs.*.subheading' => ['nullable', 'string', 'max:255'],
            'paragraphs.*.body' => ['nullable', 'string'],
            'paragraphs.*.image' => ['nullable', 'image', 'max:4096'],
            'paragraphs.*.remove_image' => ['nullable', 'boolean'],
        ]);

        if ($page !== null && $this->wouldCreateCircularParent($page, (int) ($validated['parent_id'] ?? 0))) {
            throw ValidationException::withMessages([
                'parent_id' => 'A page cannot be nested beneath itself or one of its sub-pages.',
            ]);
        }

        if ($this->wouldCreateNestedSubPage($validated, $page)) {
            throw ValidationException::withMessages([
                'parent_id' => 'Sub-pages cannot have their own sub-pages.',
            ]);
        }

        return $validated;
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
            'parent_id' => filled($validated['parent_id'] ?? null) ? (int) $validated['parent_id'] : null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
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
            $body = CmsHtmlSanitizer::sanitize(Arr::get($paragraphInput, 'body'));

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
                'sort_order' => (int) Arr::get($paragraphInput, 'sort_order', $index),
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

    /**
     * Get the next order value for a sibling group.
     */
    private function nextSortOrder(?int $parentId): int
    {
        return ((int) CmsPage::query()
            ->where('parent_id', $parentId)
            ->max('sort_order')) + 1;
    }

    /**
     * Get published sub-pages for a top-level page.
     *
     * @return Collection<int, CmsPage>
     */
    private function publishedSubPages(CmsPage $topLevelPage): Collection
    {
        return $topLevelPage->children()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();
    }

    /**
     * Get possible parent pages for the page form.
     *
     * @return Collection<int, CmsPage>
     */
    private function parentPageOptions(?CmsPage $page = null): Collection
    {
        $pages = CmsPage::query()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        if ($page === null || ! $page->exists) {
            return $pages;
        }

        $excludedIds = collect([$page->id])
            ->merge($this->descendantIds($page, $pages))
            ->all();

        return $pages
            ->reject(fn (CmsPage $option): bool => in_array($option->id, $excludedIds, true))
            ->values();
    }

    /**
     * Determine whether assigning a parent would create a page cycle.
     */
    private function wouldCreateCircularParent(CmsPage $page, int $parentId): bool
    {
        if ($parentId === 0) {
            return false;
        }

        return $this->descendantIds($page, CmsPage::query()->get())
            ->push($page->id)
            ->contains($parentId);
    }

    /**
     * Determine whether page nesting would exceed the supported depth.
     *
     * @param  array<string, mixed>  $validated
     */
    private function wouldCreateNestedSubPage(array $validated, ?CmsPage $page = null): bool
    {
        $parentId = filled($validated['parent_id'] ?? null) ? (int) $validated['parent_id'] : null;

        if ($parentId === null) {
            return false;
        }

        $parent = CmsPage::query()->find($parentId);

        if ($parent?->parent_id !== null) {
            return true;
        }

        return $page !== null && $page->children()->exists();
    }

    /**
     * Get all descendant page IDs for a page.
     *
     * @param  Collection<int, CmsPage>  $pages
     * @return Collection<int, int>
     */
    private function descendantIds(CmsPage $page, Collection $pages): Collection
    {
        $children = $pages->where('parent_id', $page->id);

        return $children->flatMap(function (CmsPage $child) use ($pages): Collection {
            return collect([$child->id])->merge($this->descendantIds($child, $pages));
        })->values();
    }
}
