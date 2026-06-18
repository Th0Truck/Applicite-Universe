<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['parent_id', 'sort_order', 'title', 'slug', 'template', 'is_published'])]
class CmsPage extends Model
{
    use HasFactory;

    /**
     * Get the parent page.
     *
     * @return BelongsTo<CmsPage, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the child pages.
     *
     * @return HasMany<CmsPage, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('title');
    }

    /**
     * Get the paragraphs attached to the CMS page.
     *
     * @return HasMany<CmsParagraph, $this>
     */
    public function paragraphs(): HasMany
    {
        return $this->hasMany(CmsParagraph::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }
}
