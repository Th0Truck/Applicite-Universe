<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CmsPage;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menu visibility must remain independent of public page access.
 */
class CmsPageMenuVisibilityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Hidden pages and sub-pages are excluded from navigation but retain their URLs.
     */
    public function test_hidden_pages_remain_accessible_by_link(): void
    {
        $parent = CmsPage::create([
            'title' => 'Visible parent', 'slug' => 'parent', 'template' => 'standard', 'is_published' => true,
        ]);
        $hidden = CmsPage::create([
            'title' => 'Hidden page', 'slug' => 'hidden', 'template' => 'standard',
            'is_published' => true, 'show_in_menu' => false,
        ]);
        $child = CmsPage::create([
            'title' => 'Hidden child', 'slug' => 'child', 'template' => 'standard',
            'parent_id' => $parent->id, 'is_published' => true, 'show_in_menu' => false,
        ]);

        $this->get('/')->assertOk()->assertSee('Visible parent')
            ->assertDontSee('Hidden page')->assertDontSee('Hidden child');
        $this->get(route('cms.pages.show', $hidden->slug))->assertOk()->assertSee('Hidden page');
        $this->get(route('cms.pages.show', $child->slug))->assertOk()->assertSee('Hidden child');

        $hidden->update(['is_published' => false]);
        $this->get(route('cms.pages.show', $hidden->slug))->assertNotFound();
    }

    /**
     * Editors can toggle visibility; older clients preserve the stored setting.
     */
    public function test_editor_can_toggle_menu_visibility(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $page = CmsPage::create([
            'title' => 'Menu page', 'slug' => 'menu-page', 'template' => 'standard', 'is_published' => true,
        ]);
        $this->assertTrue($page->fresh()->show_in_menu);
        $payload = ['title' => $page->title, 'slug' => $page->slug, 'template' => 'standard', 'is_published' => '1'];
        $this->actingAs($admin)->get(route('dashboard.cms.pages.edit', $page))
            ->assertOk()->assertSee('Show in top menu');

        foreach (['0', '1', '0'] as $value) {
            $this->put(route('dashboard.cms.pages.update', $page), $payload + ['show_in_menu' => $value])
                ->assertSessionHasNoErrors()->assertRedirect();
            $this->assertSame($value === '1', $page->fresh()->show_in_menu);
            $this->assertTrue($page->fresh()->is_published);
        }

        $this->put(route('dashboard.cms.pages.update', $page), $payload)
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertFalse($page->fresh()->show_in_menu);
        $this->put(route('dashboard.cms.pages.update', $page), $payload + ['show_in_menu' => 'invalid'])
            ->assertSessionHasErrors('show_in_menu');

        $this->actingAs(User::factory()->create())
            ->put(route('dashboard.cms.pages.update', $page), $payload + ['show_in_menu' => '1'])
            ->assertForbidden();
        $this->assertFalse($page->fresh()->show_in_menu);
    }
}
