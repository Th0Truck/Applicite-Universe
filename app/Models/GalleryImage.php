<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['path', 'original_name', 'alt_text'])]
class GalleryImage extends Model
{
    /**
     * Get the galleries that use the image.
     *
     * @return BelongsToMany<Gallery, $this>
     */
    public function galleries(): BelongsToMany
    {
        return $this->belongsToMany(Gallery::class)
            ->withPivot('sort_order')
            ->withTimestamps();
    }
}
