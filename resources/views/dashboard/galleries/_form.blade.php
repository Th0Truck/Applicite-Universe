<div class="form-grid">
    <div class="form-group">
        <label for="title">Title</label>
        <input class="form-control text-[#706f6c] dark:text-[#A1A09A]" id="title" name="title" type="text" value="{{ old('title', $gallery->title) }}" required>
    </div>

    <div class="form-group">
        <label for="slug">Slug</label>
        <input class="form-control text-[#706f6c] dark:text-[#A1A09A]" id="slug" name="slug" type="text" value="{{ old('slug', $gallery->slug) }}" placeholder="generated-from-title">
    </div>

    <div class="form-group form-group--full">
        <label for="description">Description</label>
        <textarea class="form-control text-[#706f6c] dark:text-[#A1A09A]" id="description" name="description" rows="4">{{ old('description', $gallery->description) }}</textarea>
    </div>

    <input type="hidden" name="is_published" value="0">
    <label class="checkbox-line" for="is_published">
        <input type="checkbox" id="is_published" name="is_published" value="1" @checked(old('is_published', $gallery->is_published))>
        <span class="form-label text-[#706f6c] dark:text-[#A1A09A]">Published</span>
    </label>
</div>

<div class="button-row">
    <button class="button" type="submit">{{ $buttonLabel }}</button>
    <a class="button button--secondary" href="{{ route('dashboard.galleries.index') }}">Cancel</a>
</div>
