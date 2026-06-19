<?php

namespace Tests\Feature;

use App\Models\CmsPage;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardFooterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Dashboard pages include the footer link to the internal styleguide.
     */
    public function test_dashboard_pages_include_styleguide_footer_link(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $page = CmsPage::create([
            'title' => 'Footer Test Page',
            'slug' => 'footer-test-page',
            'template' => 'standard',
            'is_published' => false,
        ]);

        $routes = [
            route('dashboard'),
            route('dashboard.users.index'),
            route('dashboard.users.edit', $admin),
            route('dashboard.cms.pages.index'),
            route('dashboard.cms.pages.create'),
            route('dashboard.cms.pages.edit', $page),
        ];

        foreach ($routes as $route) {
            $this->actingAs($admin)
                ->get($route)
                ->assertOk()
                ->assertSee('Styleguide')
                ->assertSee(route('styleguide'));
        }
    }
}
