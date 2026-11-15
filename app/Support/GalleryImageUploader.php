<?php

namespace App\Support;

use App\Models\GalleryImage;
use Illuminate\Http\UploadedFile;

class GalleryImageUploader
{
    /**
     * Store an uploaded gallery image and persist its metadata.
     */
    public function upload(UploadedFile $file, ?string $altText = null): GalleryImage
    {
        return GalleryImage::create([
            'path' => $file->store('galleries', 'public'),
            'original_name' => $file->getClientOriginalName(),
            'alt_text' => $altText,
        ]);
    }
}
