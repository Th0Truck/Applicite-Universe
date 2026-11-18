<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Validate gallery details independently of image uploads and attachments.
 */
class SaveGalleryRequest extends FormRequest
{
    /**
     * Require permission for the requested gallery operation.
     */
    public function authorize(): bool
    {
        return $this->user()?->can($this->route('gallery') ? 'galleries.update' : 'galleries.create') ?? false;
    }

    /**
     * Normalize the stored slug before checking uniqueness.
     */
    protected function prepareForValidation(): void
    {
        $slug = $this->input('slug');
        $title = $this->input('title');

        if (($slug === null || is_string($slug)) && is_string($title)) {
            $this->merge(['slug' => Str::slug(trim($slug ?? '') !== '' ? $slug : $title)]);
        }
    }

    /**
     * Define validation rules for gallery metadata.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required', 'string', 'max:255',
                Rule::unique('galleries', 'slug')->ignore($this->route('gallery')?->id),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}
