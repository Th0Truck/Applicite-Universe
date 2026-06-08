<?php

namespace App\Providers;

use App\Models\CmsPage;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user, string $ability): ?bool {
            return $user->hasRole('super_admin') ? true : null;
        });

        View::composer('components.topbar', function ($view): void {
            if (! Schema::hasTable('cms_pages')) {
                $view->with('topbarPages', collect());

                return;
            }

            $view->with('topbarPages', CmsPage::query()
                ->with(['children' => fn ($query) => $query
                    ->where('is_published', true)
                    ->orderBy('sort_order')
                    ->orderBy('title')])
                ->whereNull('parent_id')
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get());
        });
    }
}
