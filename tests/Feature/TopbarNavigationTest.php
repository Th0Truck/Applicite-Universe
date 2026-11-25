<?php

namespace Tests\Feature;

use App\Models\CmsPage;
use App\Models\Gallery;
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
    /**
     * Galleries have a separate dropdown containing only published destinations.
     */
    public function test_gallery_dropdown_lists_only_published_galleries(): void
    {
        Gallery::create(['title' => 'Winter gallery', 'slug' => 'winter', 'is_published' => true]);
        Gallery::create(['title' => 'Autumn gallery', 'slug' => 'autumn', 'is_published' => true]);
        Gallery::create(['title' => 'Private gallery', 'slug' => 'private', 'is_published' => false]);

        $this->get('/')->assertOk()
            ->assertSee('<span>Galleries</span>', false)
            ->assertSeeInOrder(['Autumn gallery', 'Winter gallery'])
            ->assertSee(route('galleries.show', 'autumn'))
            ->assertSee(route('galleries.show', 'winter'))
            ->assertDontSee('Private gallery');

        $this->get(route('login'))->assertOk()->assertDontSee('<span>Galleries</span>', false);
        $this->actingAs(User::factory()->create())->get(route('dashboard'))->assertOk()
            ->assertDontSee('<span>Galleries</span>', false)
            ->assertDontSee(route('galleries.show', 'autumn'));
        $this->get('/')->assertOk()->assertSee('<span>Galleries</span>', false);
    }

    /**
     * Do not display an empty gallery dropdown.
     */
    public function test_gallery_dropdown_is_hidden_without_published_galleries(): void
    {
        Gallery::create(['title' => 'Draft gallery', 'slug' => 'draft', 'is_published' => false]);
        $this->get('/')->assertOk()->assertDontSee('<span>Galleries</span>', false);
    }

    /**
     * Gallery links identify the current page without confusing matching CMS slugs.
     */
    public function test_gallery_dropdown_marks_current_gallery(): void
    {
        Gallery::create(['title' => 'Shared gallery', 'slug' => 'shared', 'is_published' => true]);
        CmsPage::create([
            'title' => 'Shared CMS page', 'slug' => 'shared', 'template' => 'standard', 'is_published' => true,
        ]);

        $this->get(route('galleries.show', 'shared'))->assertOk()
            ->assertSee('aria-current="page"', false)
            ->assertDontSee('class="universe-topbar__link universe-topbar__link--primary" href="'
                . route('cms.pages.show', 'shared') . '"', false);
        $this->get(route('cms.pages.show', 'shared'))->assertOk()
            ->assertSee('<span>Galleries</span>', false)
            ->assertDontSee('aria-current="page"', false);
    }
}
