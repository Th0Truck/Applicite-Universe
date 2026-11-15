<div class="form-grid">
    <div class="form-group">
        <label for="title">Title</label>
        <input class="form-control" id="title" name="title" type="text" value="{{ old('title', $gallery->title) }}" required>
    </div>

    <div class="form-group">
        <label for="slug">Slug</label>
        <input class="form-control" id="slug" name="slug" type="text" value="{{ old('slug', $gallery->slug) }}" placeholder="generated-from-title">
    </div>

    <div class="form-group form-group--full">
        <label for="description">Description</label>
        <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $gallery->description) }}</textarea>
    </div>

    <label class="checkbox-line">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $gallery->is_published))>
        Published
    </label>
</div>

<div class="paragraphs-header">
    <div>
        <h2>Upload images</h2>
        <p>Add new images to the gallery library and attach them to this gallery.</p>
    </div>
</div>

<div class="form-grid">
    <div class="form-group form-group--full">
        <label for="new_images">Images</label>
        <input class="form-control" id="new_images" name="new_images[]" type="file" accept="image/*" multiple>
    </div>

    <div class="form-group form-group--full">
        <label for="new_image_alt_text">Alt text for uploaded images</label>
        <input class="form-control" id="new_image_alt_text" name="new_image_alt_text" type="text" value="{{ old('new_image_alt_text') }}">
    </div>
</div>

<div class="paragraphs-header">
    <div>
        <h2>Attached images</h2>
        <p>Select images that should belong to this gallery.</p>
    </div>
</div>

<div class="gallery-image-grid">
    @forelse ($images as $image)
        <label class="gallery-image-option">
            <input
                type="checkbox"
                name="image_ids[]"
                value="{{ $image->id }}"
                @checked(in_array($image->id, old('image_ids', $attachedImageIds->all()), true))
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
    <button class="button" type="submit">{{ $buttonLabel }}</button>
    <a class="button button--secondary" href="{{ route('dashboard.galleries.index') }}">Cancel</a>
</div>
