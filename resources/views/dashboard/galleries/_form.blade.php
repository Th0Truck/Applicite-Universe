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

<div class="button-row">
    <button class="button" type="submit">{{ $buttonLabel }}</button>
    <a class="button button--secondary" href="{{ route('dashboard.galleries.index') }}">Cancel</a>
</div>
