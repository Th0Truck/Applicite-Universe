<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\CmsPage;
use App\Support\CmsHtmlSanitizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

/**
 * Persist page content atomically and retain existing files until writes succeed.
 */
class SaveCmsPage
{
    /**
     * Save validated content, removing new uploads if persistence fails.
     *
     * @param array<string, mixed> $attributes
     */
    public function save(CmsPage $page, array $attributes): CmsPage
    {
        $uploadedPaths = [];
        $obsoletePaths = [];

        try {
            DB::transaction(function () use ($page, $attributes, &$uploadedPaths, &$obsoletePaths): void {
                $page->fill([
                    'parent_id' => $attributes['parent_id'] ?? null,
                    'sort_order' => $attributes['sort_order'] ?? 0,
                    'title' => $attributes['title'],
                    'slug' => $attributes['slug'],
                    'template' => $attributes['template'],
                    'is_published' => (bool) ($attributes['is_published'] ?? false),
                ])->save();

                $this->syncParagraphs($page, $attributes['paragraphs'] ?? [], $uploadedPaths, $obsoletePaths);
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($uploadedPaths);

            throw $exception;
        }

        Storage::disk('public')->delete($obsoletePaths);

        return $page;
    }

    /**
     * Synchronize paragraph content while tracking file changes for cleanup.
     *
     * @param array<int|string, array<string, mixed>> $inputs
     * @param list<string> $uploadedPaths
     * @param list<string> $obsoletePaths
     */
    private function syncParagraphs(CmsPage $page, array $inputs, array &$uploadedPaths, array &$obsoletePaths): void
    {
        $existing = $page->paragraphs()->get()->keyBy('id');
        $keptIds = [];

        foreach (array_values($inputs) as $index => $input) {
            $paragraph = $existing->get($input['id'] ?? null) ?? $page->paragraphs()->make();
            $heading = trim((string) ($input['heading'] ?? ''));
            $subheading = trim((string) ($input['subheading'] ?? ''));
            $body = CmsHtmlSanitizer::sanitize($input['body'] ?? null);
            $imagePath = ! empty($input['remove_image']) ? null : $paragraph->image_path;
            $image = $input['image'] ?? null;

            if ($image instanceof UploadedFile) {
                $imagePath = $image->store('cms', 'public');

                if ($imagePath === false) {
                    throw new RuntimeException('Unable to store the CMS paragraph image.');
                }

                $uploadedPaths[] = $imagePath;
            }

            if ($heading === '' && $subheading === '' && $body === '' && $imagePath === null) {
                continue;
            }

            if ($paragraph->image_path !== null && $paragraph->image_path !== $imagePath) {
                $obsoletePaths[] = $paragraph->image_path;
            }

            $paragraph->fill([
                'sort_order' => (int) Arr::get($input, 'sort_order', $index),
                'heading' => $heading,
                'subheading' => $subheading,
                'body' => $body,
                'image_path' => $imagePath,
            ])->save();
            $keptIds[] = $paragraph->id;
        }

        foreach ($existing as $paragraph) {
            if (! in_array($paragraph->id, $keptIds, true)) {
                if ($paragraph->image_path !== null) {
                    $obsoletePaths[] = $paragraph->image_path;
                }

                $paragraph->delete();
            }
        }
    }
}
