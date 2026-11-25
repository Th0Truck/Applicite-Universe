<?php

namespace App\Http\Controllers;

use App\Actions\SaveCmsPage;
use App\Http\Requests\SaveCmsPageRequest;
use App\Models\CmsPage;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

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
    public function store(SaveCmsPageRequest $request, SaveCmsPage $savePage): RedirectResponse
    {
        $page = $savePage->save(new CmsPage(), $request->validated());

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
    public function update(SaveCmsPageRequest $request, CmsPage $page, SaveCmsPage $savePage): RedirectResponse
    {
        $savePage->save($page, $request->validated());

        return redirect()
            ->route('dashboard.cms.pages.edit', $page)
            ->with('status', 'Page updated.');
    }

    /**
     * Delete a CMS page.
     */
    public function destroy(CmsPage $page): RedirectResponse
    {
        $images = $page->paragraphs()->whereNotNull('image_path')->pluck('image_path')->all();

        DB::transaction(fn () => $page->delete());
        Storage::disk('public')->delete($images);

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

        return $pages
            ->reject(fn (CmsPage $option): bool => $option->is($page))
            ->values();
    }
}
