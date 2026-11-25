<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate and authorize a gallery image operation before any changes are made.
 */
class UploadGalleryImagesRequest extends FormRequest
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
            'new_images' => ['required', 'array', 'min:1'],
            'new_images.*' => ['required', 'image', 'max:4096'],
            'new_image_alt_text' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Explain upload errors without exposing internal input names.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'new_images.*.uploaded' => 'Image :position could not be uploaded. '
                . 'The server upload limit is ' . ini_get('upload_max_filesize')
                . ' per file. Choose a smaller image or contact the administrator.',
            'new_images.*.max' => 'Image :position must not exceed 4 MB.',
        ];
    }
}
