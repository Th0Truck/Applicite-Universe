<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CmsPage;
use Database\Seeders\FooterPagesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verify footer destinations and safe repeated creation of starter pages.
 */
class FooterPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Footer links resolve to published pages, with an empty About page.
     */
    public function test_footer_pages_are_public_and_seeding_is_repeatable(): void
    {
        $this->seed(FooterPagesSeeder::class);
        $this->seed(FooterPagesSeeder::class);

        foreach (['about', 'privacy-policy', 'terms-of-service'] as $slug) {
            $this->get(route('cms.pages.show', $slug))->assertOk()->assertSee('Site footer');
        }

        $this->assertDatabaseCount('cms_pages', 3);
        $this->assertDatabaseCount('cms_paragraphs', 2);
        $this->assertSame(0, CmsPage::where('slug', 'about')->firstOrFail()->paragraphs()->count());
    }

    /**
     * Existing content and publication choices belong to the editor.
     */
    public function test_existing_pages_are_not_overwritten(): void
    {
        $page = CmsPage::create([
            'title' => 'Our policy', 'slug' => 'privacy-policy', 'template' => 'standard', 'is_published' => false,
        ]);
        $paragraph = $page->paragraphs()->create(['heading' => '', 'body' => '<p>Custom policy</p>']);

        $this->seed(FooterPagesSeeder::class);

        $this->assertSame('Our policy', $page->fresh()->title);
        $this->assertFalse($page->fresh()->is_published);
        $this->assertSame('<p>Custom policy</p>', $paragraph->fresh()->body);
        $this->assertSame(1, $page->paragraphs()->count());
    }
}
