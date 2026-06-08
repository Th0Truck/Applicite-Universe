<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'slug', 'template', 'is_published'])]
class CmsPage extends Model
{
    use HasFactory;

    /**
     * Get the paragraphs attached to the CMS page.
     *
     * @return HasMany<CmsParagraph, $this>
     */
    public function paragraphs(): HasMany
    {
        return $this->hasMany(CmsParagraph::class)->orderBy('sort_order');
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
