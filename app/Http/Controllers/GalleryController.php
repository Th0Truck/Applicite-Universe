<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Contracts\View\View;

class GalleryController extends Controller
{
    /**
     * Display a published gallery in the frontend.
     */
    public function show(string $slug): View
    {
        $gallery = Gallery::query()
            ->published()
            ->with('images')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('galleries.show', [
            'gallery' => $gallery,
            'images' => $gallery->images,
        ]);
    }
}
