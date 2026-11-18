<section aria-label="Gallery images">
    <form method="POST" action="{{ route('dashboard.galleries.images.store', $gallery) }}" enctype="multipart/form-data">
        @csrf
        <div class="paragraphs-header">
            <div>
                <h2>Upload images</h2>
                <p>Add new images to the gallery library and attach them to this gallery.</p>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group form-group--full">
                <label for="new_images">Images</label>
                <input class="form-control" id="new_images" name="new_images[]" type="file" accept="image/*" multiple required aria-describedby="image-upload-help">
                <small id="image-upload-help">Select one or more images, up to 4 MB each.</small>
            </div>

            <div class="form-group form-group--full">
                <label for="new_image_alt_text">Alt text for uploaded images</label>
                <input class="form-control" id="new_image_alt_text" name="new_image_alt_text" type="text" value="{{ old('new_image_alt_text') }}">
            </div>
        </div>

        <div class="button-row">
            <button class="button" type="submit">Upload and attach images</button>
        </div>
    </form>

    <form method="POST" action="{{ route('dashboard.galleries.images.update', $gallery) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="image_selection" value="1">
        <div class="paragraphs-header">
            <div>
                <h2>Attached images</h2>
                <p>Select images that should belong to this gallery. Unchecking an image removes it from this gallery only.</p>
            </div>
        </div>

        <div class="gallery-image-grid">
            @forelse ($images as $image)
                <label class="gallery-image-option">
                    <input
                        type="checkbox"
                        name="image_ids[]"
                        value="{{ $image->id }}"
                        @checked(in_array($image->id, session()->hasOldInput('image_selection') ? old('image_ids', []) : $attachedImageIds->all()))
                    >
                    <img src="{{ Storage::url($image->path) }}" alt="">
                    <span>
                        <strong>{{ $image->alt_text ?: $image->original_name ?: 'Gallery image' }}</strong>
                        <small>{{ $image->galleries_count }} {{ Str::plural('gallery', $image->galleries_count) }}</small>
                    </span>
                </label>
            @empty
                <p class="empty-state">No images have been uploaded yet.</p>
            @endforelse
        </div>

        <div class="button-row">
            <button class="button" type="submit">Save attached images</button>
        </div>
    </form>
</section>
