<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styleguide - Universe</title>
    @vite('resources/css/styleguide.css')
</head>
<body class="has-universe-topbar">
    @include('components.topbar')

    <main class="styleguide-shell">
        <header class="styleguide-hero">
            <p class="styleguide-eyebrow">Internal design system</p>
            <h1>Universe Styleguide</h1>
            <p>
                Practical rules, tokens, and reusable interface patterns for building calm,
                readable, trustworthy pages across the Universe application.
            </p>
        </header>

        <x-styleguide.section title="Brand Principles" eyebrow="01">
            <div class="styleguide-grid">
                <article class="demo-card">
                    <h3>Clear before clever</h3>
                    <p>Interfaces should explain state, hierarchy, and next actions without requiring extra instruction.</p>
                </article>
                <article class="demo-card">
                    <h3>Calm operational focus</h3>
                    <p>Use neutral surfaces, restrained color, and predictable layouts for repeated admin workflows.</p>
                </article>
                <article class="demo-card">
                    <h3>Trust through consistency</h3>
                    <p>Reuse tokens, button styles, table structures, and form patterns instead of one-off styling.</p>
                </article>
            </div>
        </x-styleguide.section>

        <x-styleguide.section title="Color Palette" eyebrow="02">
            <div class="styleguide-grid">
                <x-styleguide.color-swatch name="Background" token="--color-bg" hex="#f6f7fb" />
                <x-styleguide.color-swatch name="Surface" token="--color-surface" hex="#ffffff" />
                <x-styleguide.color-swatch name="Text" token="--color-text" hex="#172033" />
                <x-styleguide.color-swatch name="Muted text" token="--color-text-muted" hex="#526071" />
                <x-styleguide.color-swatch name="Primary" token="--color-primary" hex="#2f56d9" />
                <x-styleguide.color-swatch name="Success" token="--color-success" hex="#047857" />
                <x-styleguide.color-swatch name="Border" token="--color-border" hex="#d8deea" />
                <x-styleguide.color-swatch name="Danger" token="--color-danger" hex="#991b1b" />
            </div>
        </x-styleguide.section>

        <x-styleguide.section title="Typography Scale" eyebrow="03">
            <div>
                <div class="type-sample"><code>--text-3xl</code><strong style="font-size: var(--text-3xl);">Page title, 32px</strong></div>
                <div class="type-sample"><code>--text-2xl</code><strong style="font-size: var(--text-2xl);">Section heading, 24px</strong></div>
                <div class="type-sample"><code>--text-xl</code><strong style="font-size: var(--text-xl);">Panel heading, 20px</strong></div>
                <div class="type-sample"><code>--text-base</code><span style="font-size: var(--text-base);">Body copy, 16px</span></div>
                <div class="type-sample"><code>--text-sm</code><span style="font-size: var(--text-sm);">Help text and metadata, 14px</span></div>
                <div class="type-sample"><code>--text-xs</code><span style="font-size: var(--text-xs);">Badges and labels, 12px</span></div>
            </div>
        </x-styleguide.section>

        <x-styleguide.section title="Spacing Scale" eyebrow="04">
            <div class="styleguide-grid">
                @foreach ([1 => 4, 2 => 8, 3 => 12, 4 => 16, 5 => 20, 6 => 24, 8 => 32, 10 => 40, 12 => 48] as $token => $pixels)
                    <div class="space-sample">
                        <code>--space-{{ $token }}</code>
                        <span style="--sample-width: {{ $pixels * 3 }}px;"></span>
                    </div>
                @endforeach
            </div>
        </x-styleguide.section>

        <x-styleguide.section title="Buttons" eyebrow="05">
            <div class="button-row">
                <a class="button" href="#">Primary action</a>
                <button class="button button--secondary" type="button">Secondary action</button>
                <button class="button button--ghost" type="button">Ghost action</button>
            </div>
            <p>Use one primary action per focused surface. Secondary and ghost actions should support, not compete.</p>
        </x-styleguide.section>

        <x-styleguide.section title="Form Elements" eyebrow="06">
            <form class="form-grid">
                <div class="form-group">
                    <label for="styleguide-name">Name</label>
                    <input class="form-control" id="styleguide-name" type="text" value="Example page">
                    <span class="form-help">Labels are required and help text is optional.</span>
                </div>
                <div class="form-group">
                    <label for="styleguide-template">Template</label>
                    <select class="form-select" id="styleguide-template">
                        <option>Standard</option>
                        <option>Feature</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="styleguide-notes">Notes</label>
                    <textarea class="form-textarea" id="styleguide-notes">Keep copy concise and action oriented.</textarea>
                </div>
                <label class="checkbox-line">
                    <input type="checkbox" checked>
                    <span>Published</span>
                </label>
            </form>
        </x-styleguide.section>

        <x-styleguide.section title="Cards" eyebrow="07">
            <div class="styleguide-grid">
                <article class="demo-card">
                    <h3>Default card</h3>
                    <p>Use cards for repeated items, summaries, and bounded tool surfaces.</p>
                </article>
                <article class="demo-card">
                    <h3>Content card</h3>
                    <p>Keep corners modest, borders visible, and shadows minimal.</p>
                </article>
            </div>
        </x-styleguide.section>

        <x-styleguide.section title="Badges" eyebrow="08">
            <div class="badge-row">
                <span class="badge">Draft</span>
                <span class="badge badge--success">Published</span>
                <span class="badge badge--primary">Admin</span>
            </div>
        </x-styleguide.section>

        <x-styleguide.section title="Alerts" eyebrow="09">
            <div class="alert-stack">
                <div class="alert alert--success"><strong>Saved</strong>The change was stored successfully.</div>
                <div class="alert alert--warning"><strong>Review needed</strong>Check missing fields before publishing.</div>
                <div class="alert alert--danger"><strong>Could not save</strong>Resolve validation errors and try again.</div>
            </div>
        </x-styleguide.section>

        <x-styleguide.section title="Tables" eyebrow="10">
            <table class="styleguide-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Owner</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>About us</td>
                        <td><span class="badge badge--success">Published</span></td>
                        <td>Admin</td>
                        <td>Today</td>
                    </tr>
                    <tr>
                        <td>Campaign draft</td>
                        <td><span class="badge">Draft</span></td>
                        <td>Editor</td>
                        <td>Yesterday</td>
                    </tr>
                </tbody>
            </table>
        </x-styleguide.section>

        <x-styleguide.section title="Layout Examples" eyebrow="11">
            <div class="styleguide-grid styleguide-grid--wide">
                <div class="layout-demo">
                    <div class="layout-demo__block">Single column content</div>
                    <div class="layout-demo__block">Stack sections with generous vertical spacing</div>
                </div>
                <div class="layout-demo layout-demo--two">
                    <div class="layout-demo__block">Primary content</div>
                    <div class="layout-demo__block">Supporting panel</div>
                </div>
            </div>
        </x-styleguide.section>

        <x-styleguide.section title="Accessibility Rules" eyebrow="12">
            <ul class="styleguide-list">
                <li>Every form control must have a visible label connected with <code>for</code> and <code>id</code>.</li>
                <li>Interactive elements must be keyboard reachable and use native buttons or links.</li>
                <li>Color must never be the only signal for status or validation.</li>
                <li>Text should meet WCAG AA contrast against its background.</li>
                <li>Headings should follow page structure instead of visual size alone.</li>
                <li>Focus states must remain visible and should not be removed.</li>
            </ul>
        </x-styleguide.section>
    </main>
</body>
</html>
