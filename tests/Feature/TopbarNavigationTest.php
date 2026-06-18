<?php

namespace Tests\Feature;

use App\Models\CmsPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopbarNavigationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guests see authentication actions in the topbar.
     */
    public function test_guest_sees_login_and_signup_links(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Login')
            ->assertSee('Sign up')
            ->assertDontSee('Dashboard');
    }

    /**
     * Published CMS pages are shown in hierarchical order in the topbar.
     */
    public function test_topbar_shows_published_pages_in_hierarchical_order(): void
    {
        $about = CmsPage::create([
            'title' => 'About',
            'slug' => 'about',
            'sort_order' => 1,
            'template' => 'standard',
            'is_published' => true,
        ]);

        CmsPage::create([
            'parent_id' => $about->id,
            'title' => 'Team',
            'slug' => 'team',
            'sort_order' => 1,
            'template' => 'standard',
            'is_published' => true,
        ]);

        $news = CmsPage::create([
            'title' => 'News',
            'slug' => 'news',
            'sort_order' => 0,
            'template' => 'standard',
            'is_published' => true,
        ]);

        CmsPage::create([
            'parent_id' => $news->id,
            'title' => 'Draft Update',
            'slug' => 'draft-update',
            'template' => 'standard',
            'is_published' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(['News', 'About', 'Team'])
            ->assertSee(route('cms.pages.show', 'about'))
            ->assertSee(route('cms.pages.show', 'team'))
            ->assertDontSee('Draft Update');
    }

    /**
     * Authenticated users see account actions without admin links in the main topbar.
     */
    public function test_authenticated_user_sees_account_actions_with_dashboard_in_account_menu(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();

        $content = $response->getContent();

        $this->assertStringContainsString($user->name, $content);
        $this->assertStringContainsString('Appearance', $content);
        $this->assertStringContainsString('Dashboard', $content);
        $this->assertStringContainsString(route('dashboard'), $content);
        $this->assertStringContainsString('Logout', $content);
    }

    /**
     * Dashboard is only available to authenticated users.
     */
    public function test_dashboard_requires_authentication(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Welcome back, ' . $user->name);
    }
}
