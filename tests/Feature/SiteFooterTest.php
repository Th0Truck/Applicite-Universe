<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CmsPage;
use App\Models\Gallery;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verify the shared footer on public, authentication and dashboard pages.
 */
class SiteFooterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Public pages show site information even without CMS sub-pages.
     */
    public function test_public_pages_include_one_site_footer(): void
    {
        $gallery = Gallery::create(['title' => 'Gallery', 'slug' => 'gallery', 'is_published' => true]);
        $urls = ['/', '/login', '/register', route('galleries.show', $gallery->slug)];

        foreach (['standard', 'feature'] as $template) {
            $page = CmsPage::create([
                'title' => 'Page', 'slug' => $template, 'template' => $template, 'is_published' => true,
            ]);
            $urls[] = route('cms.pages.show', $page->slug);
        }

        foreach ($urls as $url) {
            $response = $this->get($url)->assertOk()
                ->assertSee('En del af Applicite Universe.')
                ->assertSee(route('cms.pages.show', 'privacy-policy'));
            $this->assertSame(1, substr_count($response->getContent(), 'aria-label="Site footer"'));
        }
    }

    /**
     * Dashboard sections retain their existing footer and add shared site information.
     */
    public function test_dashboard_pages_include_shared_site_information(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
        $page = CmsPage::create(['title' => 'Page', 'slug' => 'page', 'template' => 'standard']);
        $gallery = Gallery::create(['title' => 'Gallery', 'slug' => 'gallery']);

        foreach (
            [
            route('dashboard'), route('dashboard.users.index'), route('dashboard.users.edit', $admin),
            route('dashboard.cms.pages.index'), route('dashboard.cms.pages.create'),
            route('dashboard.cms.pages.edit', $page), route('dashboard.galleries.index'),
            route('dashboard.galleries.create'), route('dashboard.galleries.edit', $gallery),
            route('styleguide'), route('two-factor.show'),
            ] as $url
        ) {
            $response = $this->get($url)->assertOk()->assertSee('En del af Applicite Universe.');
            $this->assertSame(1, substr_count($response->getContent(), 'aria-label="Site footer"'));
        }
    }
}
