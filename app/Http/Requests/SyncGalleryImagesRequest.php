<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate and authorize a gallery image operation before any changes are made.
 */
class SyncGalleryImagesRequest extends FormRequest
{
    /**
     * Image changes require permission to update galleries.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('galleries.update') ?? false;
    }

    /**
     * Define the accepted image input.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'image_selection' => ['required', 'accepted'],
            'image_ids' => ['sometimes', 'array'],
            'image_ids.*' => ['required', 'integer', 'distinct', 'exists:gallery_images,id'],
        ];
    }
}
