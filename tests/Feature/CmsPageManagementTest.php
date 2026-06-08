<?php

namespace Tests\Feature;

use App\Models\CmsPage;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CmsPageManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Page management requires page view permission.
     */
    public function test_page_index_requires_page_view_permission(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.cms.pages.index'))
            ->assertForbidden();
    }

    /**
     * Admins can create CMS pages with paragraph images.
     */
    public function test_admin_can_create_page_with_paragraph_and_image(): void
    {
        Storage::fake('public');
        $this->seed(RoleAndPermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('dashboard.cms.pages.store'), [
                'title' => 'About Applicite',
                'slug' => 'about-applicite',
                'template' => 'feature',
                'is_published' => '1',
                'paragraphs' => [
                    [
                        'heading' => 'Built for useful systems',
                        'subheading' => 'CMS',
                        'body' => 'A first paragraph with useful information.',
                        'image' => UploadedFile::fake()->image('paragraph.jpg', 800, 600),
                    ],
                ],
            ])
            ->assertRedirect();

        $page = CmsPage::with('paragraphs')->where('slug', 'about-applicite')->firstOrFail();

        $this->assertTrue($page->is_published);
        $this->assertSame('feature', $page->template);
        $this->assertCount(1, $page->paragraphs);
        $this->assertSame('Built for useful systems', $page->paragraphs->first()->heading);
        Storage::disk('public')->assertExists($page->paragraphs->first()->image_path);
    }

    /**
     * Published pages render through their selected template.
     */
    public function test_published_page_can_be_rendered_publicly(): void
    {
        $page = CmsPage::create([
            'title' => 'Public Page',
            'slug' => 'public-page',
            'template' => 'standard',
            'is_published' => true,
        ]);

        $page->paragraphs()->create([
            'sort_order' => 0,
            'heading' => 'Welcome',
            'subheading' => 'Intro',
            'body' => 'This is public CMS content.',
        ]);

        $this->get(route('cms.pages.show', 'public-page'))
            ->assertOk()
            ->assertSee('Public Page')
            ->assertSee('Welcome')
            ->assertSee('This is public CMS content.');
    }

    /**
     * Draft pages are not publicly available.
     */
    public function test_draft_page_is_not_publicly_available(): void
    {
        CmsPage::create([
            'title' => 'Draft Page',
            'slug' => 'draft-page',
            'template' => 'standard',
            'is_published' => false,
        ]);

        $this->get(route('cms.pages.show', 'draft-page'))
            ->assertNotFound();
    }
}
