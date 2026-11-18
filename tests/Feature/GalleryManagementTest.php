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
     * Admins create galleries before uploading and attaching images.
     */
    public function test_admin_can_create_gallery_then_upload_images(): void
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
            ])
            ->assertRedirect();

        $gallery = Gallery::with('images')->where('slug', 'summer-gallery')->firstOrFail();

        $this->assertTrue($gallery->is_published);
        $this->assertSame('Images from summer.', $gallery->description);
        $this->assertCount(0, $gallery->images);

        $this->post(route('dashboard.galleries.images.store', $gallery), [
            'new_image_alt_text' => 'Summer image',
            'new_images' => [
                UploadedFile::fake()->image('summer.jpg', 1200, 800),
                UploadedFile::fake()->image('winter.jpg', 1200, 800),
            ],
        ])->assertSessionHasNoErrors()->assertRedirect(route('dashboard.galleries.edit', $gallery));

        $gallery->load('images');
        $this->assertCount(2, $gallery->images);
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
            ->put(route('dashboard.galleries.images.update', $firstGallery), [
                'image_selection' => '1',
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

        $secondGallery = Gallery::where('slug', 'second-gallery')->firstOrFail();
        $this->put(route('dashboard.galleries.images.update', $secondGallery), [
            'image_selection' => '1',
            'image_ids' => [$image->id],
        ])->assertSessionHasNoErrors()->assertRedirect();

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

        $gallery = Gallery::create(['title' => 'Gallery', 'slug' => 'gallery']);

        $this->actingAs($admin)
            ->post(route('dashboard.galleries.images.store', $gallery), [
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
    /**
     * Gallery details never upload, attach, or detach images.
     */
    public function test_gallery_details_do_not_change_images(): void
    {
        Storage::fake('public');
        $this->seed(RoleAndPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $image = GalleryImage::create(['path' => 'galleries/existing.jpg']);

        $this->actingAs($admin)->post(route('dashboard.galleries.store'), [
            'title' => 'Gallery', 'slug' => 'gallery',
            'image_ids' => [$image->id],
            'new_images' => [UploadedFile::fake()->image('ignored.jpg')],
        ])->assertSessionHasNoErrors()->assertRedirect();

        $gallery = Gallery::where('slug', 'gallery')->firstOrFail();
        $this->assertSame(0, $gallery->images()->count());
        $this->assertSame([], Storage::disk('public')->allFiles());
        $gallery->images()->attach($image);

        $this->put(route('dashboard.galleries.update', $gallery), [
            'title' => 'Updated', 'slug' => 'gallery',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertSame('Updated', $gallery->fresh()->title);
        $this->assertSame([$image->id], $gallery->images()->pluck('gallery_images.id')->all());
        $this->assertDatabaseCount('gallery_images', 1);
    }

    /**
     * Uploading appends images; clearing a selection retains shared library files.
     */
    public function test_upload_preserves_existing_images_and_selection_can_be_cleared(): void
    {
        Storage::fake('public');
        $this->seed(RoleAndPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $gallery = Gallery::create(['title' => 'Gallery', 'slug' => 'gallery']);
        $other = Gallery::create(['title' => 'Other', 'slug' => 'other']);
        $image = GalleryImage::create(['path' => 'galleries/shared.jpg']);
        Storage::disk('public')->put($image->path, 'shared');
        $gallery->images()->attach($image);
        $other->images()->attach($image);

        $this->actingAs($admin)->post(route('dashboard.galleries.images.store', $gallery), [
            'new_images' => [UploadedFile::fake()->image('new.jpg')],
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(2, $gallery->images()->count());
        $this->assertSame($image->id, $gallery->images()->first()->id);

        $this->put(route('dashboard.galleries.images.update', $gallery), [
            'image_selection' => '1',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(0, $gallery->images()->count());
        $this->assertSame(1, $other->images()->count());
        $this->assertDatabaseCount('gallery_images', 2);
        Storage::disk('public')->assertExists($image->path);
    }

    /**
     * Image changes require update permission and valid existing image IDs.
     */
    public function test_image_endpoints_require_permission_and_validate_selection(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->givePermissionTo('galleries.create');
        $gallery = Gallery::create(['title' => 'Gallery', 'slug' => 'gallery']);

        $this->actingAs($user)->post(route('dashboard.galleries.images.store', $gallery))->assertForbidden();
        $this->put(route('dashboard.galleries.images.update', $gallery))->assertForbidden();

        $user->givePermissionTo('galleries.update');
        $this->put(route('dashboard.galleries.images.update', $gallery), [
            'image_selection' => '1', 'image_ids' => [999],
        ])->assertSessionHasErrors('image_ids.0');
        $this->post(route('dashboard.galleries.images.store', $gallery), [])
            ->assertSessionHasErrors('new_images');
        $this->post(route('dashboard.galleries.images.store', 999), [])->assertNotFound();
    }

    /**
     * Image forms only appear below the gallery details on the edit page.
     */
    public function test_image_forms_are_separate_and_only_available_after_creation(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $gallery = Gallery::create(['title' => 'Gallery', 'slug' => 'gallery']);

        $this->actingAs($admin)->get(route('dashboard.galleries.create'))
            ->assertOk()->assertDontSee('name="new_images[]"', false)->assertDontSee('name="image_selection"', false);
        $this->get(route('dashboard.galleries.edit', $gallery))->assertOk()
            ->assertSeeInOrder([
                'Save gallery',
                route('dashboard.galleries.images.store', $gallery),
                'Upload and attach images',
                'Save attached images',
            ]);
    }

    /**
     * A failure partway through a batch removes all new files and database writes.
     */
    public function test_failed_upload_batch_is_rolled_back(): void
    {
        Storage::fake('public');
        $gallery = Gallery::create(['title' => 'Gallery', 'slug' => 'gallery']);
        $event = 'eloquent.creating: ' . GalleryImage::class;
        $writes = 0;
        \Illuminate\Support\Facades\Event::listen($event, function () use (&$writes): void {
            if (++$writes === 2) {
                throw new \RuntimeException('Simulated image failure');
            }
        });

        try {
            app(\App\Support\GalleryImageUploader::class)->uploadToGallery($gallery, [
                UploadedFile::fake()->image('first.jpg'),
                UploadedFile::fake()->image('second.jpg'),
            ]);
            $this->fail('The upload failure should propagate.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Simulated image failure', $exception->getMessage());
        } finally {
            \Illuminate\Support\Facades\Event::forget($event);
        }

        $this->assertDatabaseCount('gallery_images', 0);
        $this->assertSame(0, $gallery->images()->count());
        $this->assertSame([], Storage::disk('public')->allFiles());
    }
    /**
     * PHP upload failures show a useful message instead of an internal field name.
     */
    public function test_php_upload_limit_failure_has_a_readable_message(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $gallery = Gallery::create(['title' => 'Gallery', 'slug' => 'gallery']);
        $file = new UploadedFile('', 'large.jpg', 'image/jpeg', UPLOAD_ERR_INI_SIZE, true);

        $this->actingAs($admin)->post(route('dashboard.galleries.images.store', $gallery), [
            'new_images' => [$file],
        ])->assertSessionHasErrors([
            'new_images.0' => 'Image 1 could not be uploaded. The server upload limit is '
                . ini_get('upload_max_filesize')
                . ' per file. Choose a smaller image or contact the administrator.',
        ]);

        $this->assertDatabaseCount('gallery_images', 0);
    }
}
