<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\CmsPage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Validate CMS page edits before any content or files are changed.
 */
class SaveCmsPageRequest extends FormRequest
{
    /**
     * Authorize the operation independently of route middleware.
     */
    public function authorize(): bool
    {
        return $this->user()?->can($this->route('page') ? 'pages.update' : 'pages.create') ?? false;
    }

    /**
     * Validate the same normalized slug that will be persisted.
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
     * Get page and paragraph validation rules.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $page = $this->route('page');

        return [
            'title' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:cms_pages,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cms_pages', 'slug')->ignore($page?->id),
            ],
            'template' => ['required', Rule::in(array_keys(config('cms.templates')))],
            'is_published' => ['nullable', 'boolean'],
            'show_in_menu' => ['sometimes', 'boolean'],
            'paragraphs' => ['array'],
            'paragraphs.*' => ['array'],
            'paragraphs.*.id' => [
                'nullable', 'integer', 'distinct',
                Rule::exists('cms_paragraphs', 'id')->where('cms_page_id', $page->id ?? 0),
            ],
            'paragraphs.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'paragraphs.*.heading' => ['nullable', 'string', 'max:255'],
            'paragraphs.*.subheading' => ['nullable', 'string', 'max:255'],
            'paragraphs.*.body' => ['nullable', 'string'],
            'paragraphs.*.image' => ['nullable', 'image', 'max:4096'],
            'paragraphs.*.remove_image' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Enforce the supported two-level page hierarchy after field validation.
     *
     * @return array<callable>
     */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty() || ! $this->filled('parent_id')) {
                return;
            }

            $page = $this->route('page');
            $parent = CmsPage::query()->find($this->integer('parent_id'));

            if ($page !== null && $page->is($parent)) {
                $validator->errors()->add('parent_id', 'A page cannot be its own parent.');
            } elseif ($parent?->parent_id !== null || ($page !== null && $page->children()->exists())) {
                $validator->errors()->add('parent_id', 'Sub-pages cannot have their own sub-pages.');
            }
        }];
    }
}
