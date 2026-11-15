<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Gallery management requires gallery view permission.
     */
    public function test_gallery_index_requires_gallery_view_permission(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.galleries.index'))
            ->assertForbidden();
    }

    /**
     * Admins can create galleries with uploaded images.
     */
    public function test_admin_can_create_gallery_with_uploaded_images(): void
    {
        Storage::fake('public');
        $this->seed(RoleAndPermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('dashboard.galleries.store'), [
                'title' => 'Summer gallery',
                'slug' => 'summer-gallery',
                'description' => 'Images from summer.',
                'is_published' => '1',
                'new_image_alt_text' => 'Summer image',
                'new_images' => [
                    UploadedFile::fake()->image('summer.jpg', 1200, 800),
                ],
            ])
            ->assertRedirect();

        $gallery = Gallery::with('images')->where('slug', 'summer-gallery')->firstOrFail();

        $this->assertTrue($gallery->is_published);
        $this->assertSame('Images from summer.', $gallery->description);
        $this->assertCount(1, $gallery->images);
        $this->assertSame('Summer image', $gallery->images->first()->alt_text);
        Storage::disk('public')->assertExists($gallery->images->first()->path);
    }

    /**
     * Existing gallery images can be attached to multiple galleries.
     */
    public function test_existing_image_can_be_attached_to_multiple_galleries(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $image = GalleryImage::create([
            'path' => 'galleries/shared.jpg',
            'original_name' => 'shared.jpg',
            'alt_text' => 'Shared image',
        ]);

        $firstGallery = Gallery::create([
            'title' => 'First gallery',
            'slug' => 'first-gallery',
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('dashboard.galleries.update', $firstGallery), [
                'title' => 'First gallery',
                'slug' => 'first-gallery',
                'is_published' => '1',
                'image_ids' => [$image->id],
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('dashboard.galleries.store'), [
                'title' => 'Second gallery',
                'slug' => 'second-gallery',
                'is_published' => '1',
                'image_ids' => [$image->id],
            ])
            ->assertRedirect();

        $this->assertSame(2, $image->galleries()->count());
    }

    /**
     * Gallery uploads must be images.
     */
    public function test_gallery_upload_rejects_non_images(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('dashboard.galleries.store'), [
                'title' => 'Invalid gallery',
                'slug' => 'invalid-gallery',
                'new_images' => [
                    UploadedFile::fake()->create('document.pdf', 24, 'application/pdf'),
                ],
            ])
            ->assertSessionHasErrors('new_images.0');
    }

    /**
     * Published galleries are available in the frontend.
     */
    public function test_published_gallery_is_available_in_frontend(): void
    {
        $gallery = Gallery::create([
            'title' => 'Public gallery',
            'slug' => 'public-gallery',
            'description' => 'Public description.',
            'is_published' => true,
        ]);

        $image = GalleryImage::create([
            'path' => 'galleries/public.jpg',
            'alt_text' => 'Public image',
        ]);

        $gallery->images()->attach($image, ['sort_order' => 0]);

        $this->get(route('galleries.show', 'public-gallery'))
            ->assertOk()
            ->assertSee('Public gallery')
            ->assertSee('Public description.')
            ->assertSee('Public image');
    }

    /**
     * Draft galleries are hidden from the frontend.
     */
    public function test_draft_gallery_is_not_available_in_frontend(): void
    {
        Gallery::create([
            'title' => 'Draft gallery',
            'slug' => 'draft-gallery',
            'is_published' => false,
        ]);

        $this->get(route('galleries.show', 'draft-gallery'))
            ->assertNotFound();
    }
}
