<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['title', 'slug', 'description', 'is_published'])]
class Gallery extends Model
{
    /**
     * Get the images attached to the gallery.
     *
     * @return BelongsToMany<GalleryImage, $this>
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(GalleryImage::class)
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('gallery_gallery_image.sort_order')
            ->orderBy('gallery_images.id');
    }

    /**
     * Scope the query to published galleries.
     *
     * @param  Builder<Gallery>  $query
     * @return Builder<Gallery>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
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
