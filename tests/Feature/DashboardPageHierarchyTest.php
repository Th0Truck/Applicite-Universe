<?php

namespace Tests\Feature;

use App\Models\CmsPage;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPageHierarchyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Users with page access can see created pages arranged by stored hierarchy.
     */
    public function test_dashboard_shows_created_pages_in_stored_hierarchy(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $company = CmsPage::create([
            'title' => 'Company',
            'slug' => 'company',
            'sort_order' => 0,
            'template' => 'standard',
            'is_published' => true,
        ]);

        $team = CmsPage::create([
            'parent_id' => $company->id,
            'title' => 'Company Team',
            'slug' => 'company-team',
            'sort_order' => 0,
            'template' => 'standard',
            'is_published' => false,
        ]);

        CmsPage::create([
            'parent_id' => $team->id,
            'title' => 'Company Team History',
            'slug' => 'company-team-history',
            'sort_order' => 0,
            'template' => 'standard',
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Created pages')
            ->assertSeeInOrder([
                'Company',
                '/company',
                'Company Team',
                '/company-team',
                'Company Team History',
                '/company-team-history',
            ])
            ->assertSee(route('dashboard.cms.pages.create', ['parent_id' => $company->id]))
            ->assertSee(route('dashboard.cms.pages.edit', $company) . '#paragraphs');
    }

    /**
     * Users without page access do not see created pages on the dashboard.
     */
    public function test_dashboard_hides_created_pages_without_page_permission(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $user = User::factory()->create();

        CmsPage::create([
            'title' => 'Hidden Dashboard Page',
            'slug' => 'hidden-dashboard-page',
            'template' => 'standard',
            'is_published' => false,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Created pages')
            ->assertDontSee('Hidden Dashboard Page');
    }
}
