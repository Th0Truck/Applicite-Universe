<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard.
     */
    public function __invoke(): View
    {
        return view('dashboard', [
            'pageHierarchy' => Gate::allows('pages.view') ? $this->pageHierarchy() : collect(),
        ]);
    }

    /**
     * Build the page hierarchy from stored parent relationships.
     *
     * @return Collection<int, array{page: CmsPage, depth: int}>
     */
    private function pageHierarchy(): Collection
    {
        $pages = CmsPage::query()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return $this->appendPages(
            $pages->groupBy(fn (CmsPage $page): string => $this->parentKey($page->parent_id)),
            $this->parentKey(null),
        );
    }

    /**
     * Recursively append pages beneath a parent page.
     *
     * @param  Collection<string, Collection<int, CmsPage>>  $childrenByParent
     * @return Collection<int, array{page: CmsPage, depth: int}>
     */
    private function appendPages(Collection $childrenByParent, string $parentKey, int $depth = 0): Collection
    {
        return $childrenByParent
            ->get($parentKey, collect())
            ->flatMap(function (CmsPage $page) use ($childrenByParent, $depth): Collection {
                return collect([[
                    'page' => $page,
                    'depth' => $depth,
                ]])->merge($this->appendPages($childrenByParent, $this->parentKey($page->id), $depth + 1));
            })
            ->values();
    }

    /**
     * Normalize parent keys for grouping nullable parent IDs.
     */
    private function parentKey(?int $parentId): string
    {
        return $parentId === null ? 'root' : (string) $parentId;
    }
}
