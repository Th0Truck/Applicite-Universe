@php
    $formParagraphs = old('paragraphs');

    if ($formParagraphs === null) {
        $formParagraphs = $paragraphs->map(fn ($paragraph) => [
            'id' => $paragraph->id,
            'sort_order' => $paragraph->sort_order,
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
        <label for="parent_id">Parent page</label>
        <select class="form-control" id="parent_id" name="parent_id">
            <option value="">Top-level page</option>
            @foreach ($parentPages as $parentPage)
                <option value="{{ $parentPage->id }}" @selected((string) old('parent_id', $page->parent_id) === (string) $parentPage->id)>
                    {{ $parentPage->title }} (/{{ $parentPage->slug }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="sort_order">Sub-page order</label>
        <input class="form-control" id="sort_order" name="sort_order" type="number" min="0" step="1" value="{{ old('sort_order', $page->sort_order ?? 0) }}">
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

<div class="paragraphs-header" id="paragraphs">
    <div>
        <h2>Paragraphs</h2>
        <p>Each paragraph can include a heading, subheading, text, optional image, and display order.</p>
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
                <div class="form-group form-group--order">
                    <label for="paragraph-sort-order-{{ $index }}">Order</label>
                    <input class="form-control" id="paragraph-sort-order-{{ $index }}" name="paragraphs[{{ $index }}][sort_order]" type="number" min="0" step="1" value="{{ $paragraph['sort_order'] ?? $index }}">
                </div>

                <div class="form-group">
                    <label for="paragraph-heading-{{ $index }}">Heading</label>
                    <input class="form-control" id="paragraph-heading-{{ $index }}" name="paragraphs[{{ $index }}][heading]" type="text" value="{{ $paragraph['heading'] ?? '' }}">
                </div>

                <div class="form-group">
                    <label for="paragraph-subheading-{{ $index }}">Subheading</label>
                    <input class="form-control" id="paragraph-subheading-{{ $index }}" name="paragraphs[{{ $index }}][subheading]" type="text" value="{{ $paragraph['subheading'] ?? '' }}">
                </div>

                <div class="form-group form-group--full">
                    <label id="paragraph-body-label-{{ $index }}" for="paragraph-body-{{ $index }}">Text</label>
                    <div class="rich-editor" data-rich-editor>
                        <div class="rich-editor__toolbar" aria-label="Text formatting">
                            <button class="rich-editor__button" type="button" data-editor-command="formatBlock" data-editor-value="p" title="Paragraph" aria-label="Paragraph">P</button>
                            <button class="rich-editor__button" type="button" data-editor-command="formatBlock" data-editor-value="h3" title="Heading" aria-label="Heading">H</button>
                            <button class="rich-editor__button" type="button" data-editor-command="bold" title="Bold" aria-label="Bold"><strong>B</strong></button>
                            <button class="rich-editor__button" type="button" data-editor-command="italic" title="Italic" aria-label="Italic"><em>I</em></button>
                            <button class="rich-editor__button" type="button" data-editor-command="underline" title="Underline" aria-label="Underline"><u>U</u></button>
                            <button class="rich-editor__button" type="button" data-editor-command="insertUnorderedList" title="Bulleted list" aria-label="Bulleted list">•</button>
                            <button class="rich-editor__button" type="button" data-editor-command="insertOrderedList" title="Numbered list" aria-label="Numbered list">1.</button>
                            <button class="rich-editor__button" type="button" data-editor-command="formatBlock" data-editor-value="blockquote" title="Quote" aria-label="Quote">“</button>
                            <button class="rich-editor__button" type="button" data-editor-link title="Link" aria-label="Link">↗</button>
                            <button class="rich-editor__button" type="button" data-editor-command="removeFormat" title="Clear formatting" aria-label="Clear formatting">×</button>
                        </div>

                        <div
                            class="rich-editor__surface"
                            contenteditable="true"
                            data-editor-surface
                            role="textbox"
                            aria-multiline="true"
                            aria-labelledby="paragraph-body-label-{{ $index }}"
                        >{!! \App\Support\CmsHtmlSanitizer::sanitize($paragraph['body'] ?? '') !!}</div>

                        <textarea class="rich-editor__input" id="paragraph-body-{{ $index }}" name="paragraphs[{{ $index }}][body]" rows="6" data-editor-input>{{ $paragraph['body'] ?? '' }}</textarea>
                    </div>
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

<script>
    document.querySelectorAll('[data-rich-editor]').forEach(function (editor) {
        var surface = editor.querySelector('[data-editor-surface]');
        var input = editor.querySelector('[data-editor-input]');

        if (!surface || !input) {
            return;
        }

        var syncInput = function () {
            input.value = surface.innerHTML.trim();
        };

        editor.querySelectorAll('[data-editor-command]').forEach(function (button) {
            button.addEventListener('click', function () {
                surface.focus();
                document.execCommand(button.dataset.editorCommand, false, button.dataset.editorValue || null);
                syncInput();
            });
        });

        editor.querySelectorAll('[data-editor-link]').forEach(function (button) {
            button.addEventListener('click', function () {
                var url = window.prompt('Link URL');

                if (!url) {
                    return;
                }

                surface.focus();
                document.execCommand('createLink', false, url);
                syncInput();
            });
        });

        surface.addEventListener('input', syncInput);
        surface.addEventListener('blur', syncInput);
        var form = editor.closest('form');

        if (form) {
            form.addEventListener('submit', syncInput);
        }

        syncInput();
    });
</script>
