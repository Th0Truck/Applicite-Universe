@php
    $formParagraphs = old('paragraphs');

    if ($formParagraphs === null) {
        $formParagraphs = $paragraphs->map(fn ($paragraph) => [
            'id' => $paragraph->id,
            'heading' => $paragraph->heading,
            'subheading' => $paragraph->subheading,
            'body' => $paragraph->body,
            'image_path' => $paragraph->image_path,
        ])->values()->all();
    }

    $formParagraphs[] = ['heading' => '', 'subheading' => '', 'body' => ''];
@endphp

<div class="form-grid">
    <div class="form-group">
        <label for="title">Title</label>
        <input class="form-control" id="title" name="title" type="text" value="{{ old('title', $page->title) }}" required>
    </div>

    <div class="form-group">
        <label for="slug">Slug</label>
        <input class="form-control" id="slug" name="slug" type="text" value="{{ old('slug', $page->slug) }}" placeholder="generated-from-title">
    </div>

    <div class="form-group">
        <label for="template">Template</label>
        <select class="form-control" id="template" name="template" required>
            @foreach ($templates as $key => $template)
                <option value="{{ $key }}" @selected(old('template', $page->template) === $key)>
                    {{ $template['label'] }}
                </option>
            @endforeach
        </select>
    </div>

    <label class="checkbox-line">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $page->is_published))>
        Published
    </label>
</div>

<div class="paragraphs-header">
    <div>
        <h2>Paragraphs</h2>
        <p>Each paragraph can include a heading, subheading, text, and optional image.</p>
    </div>
</div>

<div class="paragraph-stack">
    @foreach ($formParagraphs as $index => $paragraph)
        <section class="paragraph-card">
            <div class="paragraph-card__header">
                <h3>Paragraph {{ $index + 1 }}</h3>
            </div>

            @if (! empty($paragraph['id']))
                <input type="hidden" name="paragraphs[{{ $index }}][id]" value="{{ $paragraph['id'] }}">
            @endif

            <div class="form-grid">
                <div class="form-group">
                    <label for="paragraph-heading-{{ $index }}">Heading</label>
                    <input class="form-control" id="paragraph-heading-{{ $index }}" name="paragraphs[{{ $index }}][heading]" type="text" value="{{ $paragraph['heading'] ?? '' }}">
                </div>

                <div class="form-group">
                    <label for="paragraph-subheading-{{ $index }}">Subheading</label>
                    <input class="form-control" id="paragraph-subheading-{{ $index }}" name="paragraphs[{{ $index }}][subheading]" type="text" value="{{ $paragraph['subheading'] ?? '' }}">
                </div>

                <div class="form-group form-group--full">
                    <label for="paragraph-body-{{ $index }}">Text</label>
                    <textarea class="form-control" id="paragraph-body-{{ $index }}" name="paragraphs[{{ $index }}][body]" rows="6">{{ $paragraph['body'] ?? '' }}</textarea>
                </div>

                <div class="form-group form-group--full">
                    <label for="paragraph-image-{{ $index }}">Image</label>
                    <input class="form-control" id="paragraph-image-{{ $index }}" name="paragraphs[{{ $index }}][image]" type="file" accept="image/*">

                    @if (! empty($paragraph['image_path']))
                        <div class="current-image">
                            <img src="{{ Storage::url($paragraph['image_path']) }}" alt="">
                            <label class="checkbox-line">
                                <input type="checkbox" name="paragraphs[{{ $index }}][remove_image]" value="1">
                                Remove current image
                            </label>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endforeach
</div>

<div class="button-row">
    <button class="button" type="submit">{{ $buttonLabel }}</button>
    <a class="button button--secondary" href="{{ route('dashboard.cms.pages.index') }}">Cancel</a>
</div>
