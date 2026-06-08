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
     * Page forms include a rich text editor for paragraph bodies.
     */
    public function test_page_form_includes_paragraph_rich_text_editor(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('dashboard.cms.pages.create'))
            ->assertOk()
            ->assertSee('data-rich-editor', false)
            ->assertSee('data-editor-command="bold"', false)
            ->assertSee('data-editor-surface', false)
            ->assertSee('data-editor-input', false);
    }

    /**
     * Admins can create sub-pages and set paragraph order.
     */
    public function test_admin_can_create_sub_page_with_ordered_paragraphs(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $parent = CmsPage::create([
            'title' => 'News',
            'slug' => 'news',
            'template' => 'standard',
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('dashboard.cms.pages.store'), [
                'parent_id' => $parent->id,
                'sort_order' => 3,
                'title' => 'Local News',
                'slug' => 'local-news',
                'template' => 'standard',
                'is_published' => '1',
                'paragraphs' => [
                    [
                        'sort_order' => 20,
                        'heading' => 'Second paragraph',
                        'body' => 'Shown after the leading paragraph.',
                    ],
                    [
                        'sort_order' => 10,
                        'heading' => 'First paragraph',
                        'body' => 'Shown before the second paragraph.',
                    ],
                ],
            ])
            ->assertRedirect();

        $page = CmsPage::with('paragraphs')->where('slug', 'local-news')->firstOrFail();

        $this->assertSame($parent->id, $page->parent_id);
        $this->assertSame(3, $page->sort_order);
        $this->assertSame([
            'First paragraph',
            'Second paragraph',
        ], $page->paragraphs->pluck('heading')->all());
    }

    /**
     * Paragraph rich text is sanitized before public rendering.
     */
    public function test_paragraph_rich_text_is_sanitized(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('dashboard.cms.pages.store'), [
                'title' => 'Rich Text Page',
                'slug' => 'rich-text-page',
                'template' => 'standard',
                'is_published' => '1',
                'paragraphs' => [
                    [
                        'heading' => 'Formatted paragraph',
                        'body' => '<p><strong>Safe copy</strong><script>alert("bad")</script><a href="javascript:alert(1)" onclick="bad()">Unsafe link</a></p>',
                    ],
                ],
            ])
            ->assertRedirect();

        $page = CmsPage::with('paragraphs')->where('slug', 'rich-text-page')->firstOrFail();
        $body = $page->paragraphs->first()->body;

        $this->assertStringContainsString('<strong>Safe copy</strong>', $body);
        $this->assertStringContainsString('<a>Unsafe link</a>', $body);
        $this->assertStringNotContainsString('<script>', $body);
        $this->assertStringNotContainsString('onclick', $body);
        $this->assertStringNotContainsString('javascript:', $body);

        $this->get(route('cms.pages.show', 'rich-text-page'))
            ->assertOk()
            ->assertSee('<strong>Safe copy</strong>', false)
            ->assertSee('<a>Unsafe link</a>', false)
            ->assertDontSee('onclick', false)
            ->assertDontSee('javascript:', false);
    }

    /**
     * Paragraph rich text normalizes editor encoding artifacts.
     */
    public function test_paragraph_rich_text_normalizes_encoding_artifacts(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('dashboard.cms.pages.store'), [
                'title' => 'Encoding Page',
                'slug' => 'encoding-page',
                'template' => 'standard',
                'is_published' => '1',
                'paragraphs' => [
                    [
                        'heading' => 'Clean text',
                        'body' => '<p>First&nbsp;line</p><p>Second ÃÂÂ line</p>',
                    ],
                ],
            ])
            ->assertRedirect();

        $page = CmsPage::with('paragraphs')->where('slug', 'encoding-page')->firstOrFail();
        $body = $page->paragraphs->first()->body;

        $this->assertStringContainsString('First line', $body);
        $this->assertStringContainsString('Second line', $body);
        $this->assertStringNotContainsString('&nbsp;', $body);
        $this->assertStringNotContainsString('ÃÂÂ', $body);
    }

    /**
     * Pages cannot be nested beneath their own descendants.
     */
    public function test_page_parent_cannot_be_own_descendant(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $parent = CmsPage::create([
            'title' => 'Parent Page',
            'slug' => 'parent-page',
            'template' => 'standard',
        ]);

        $child = CmsPage::create([
            'parent_id' => $parent->id,
            'title' => 'Child Page',
            'slug' => 'child-page',
            'template' => 'standard',
        ]);

        $this->actingAs($admin)
            ->put(route('dashboard.cms.pages.update', $parent), [
                'parent_id' => $child->id,
                'title' => $parent->title,
                'slug' => $parent->slug,
                'template' => $parent->template,
            ])
            ->assertSessionHasErrors('parent_id');
    }

    /**
     * Sub-pages cannot have child pages.
     */
    public function test_sub_page_cannot_have_sub_pages(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $parent = CmsPage::create([
            'title' => 'Parent Page',
            'slug' => 'parent-page',
            'template' => 'standard',
        ]);

        $child = CmsPage::create([
            'parent_id' => $parent->id,
            'title' => 'Child Page',
            'slug' => 'child-page',
            'template' => 'standard',
        ]);

        $this->actingAs($admin)
            ->post(route('dashboard.cms.pages.store'), [
                'parent_id' => $child->id,
                'title' => 'Nested Child Page',
                'slug' => 'nested-child-page',
                'template' => 'standard',
            ])
            ->assertSessionHasErrors('parent_id');
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
            ->assertSee('has-universe-topbar', false)
            ->assertSee('universe-topbar', false)
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
