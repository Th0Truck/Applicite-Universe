<style>
    body {
        background: #f6f7fb;
        color: #172033;
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        margin: 0;
        min-height: 100vh;
    }

    .admin-shell {
        margin: 0 auto;
        max-width: 1180px;
        padding: 32px 20px;
    }

    .admin-panel {
        background: white;
        border: 1px solid #e6e8ef;
        border-radius: 8px;
        box-shadow: 0 8px 22px rgba(20, 24, 40, 0.08);
        overflow: hidden;
        padding: 28px;
    }

    .admin-header {
        align-items: flex-start;
        display: flex;
        gap: 16px;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .admin-header h1 {
        font-size: 24px;
        margin: 0 0 6px;
    }

    .admin-header p,
    .muted {
        color: #526071;
    }

    .admin-link,
    .link-button {
        background: transparent;
        border: 0;
        color: #2447f9;
        cursor: pointer;
        font: inherit;
        font-weight: 700;
        padding: 0;
        text-decoration: none;
    }

    .admin-link:hover,
    .link-button:hover {
        text-decoration: underline;
    }

    .admin-table {
        border-collapse: collapse;
        width: 100%;
    }

    .admin-table th,
    .admin-table td {
        border-bottom: 1px solid #edf0f5;
        padding: 14px 0;
        text-align: left;
        vertical-align: middle;
    }

    .admin-table th {
        color: #526071;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .actions-cell {
        align-items: center;
        display: flex;
        gap: 12px;
    }

    .form-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .form-group--full,
    .checkbox-line {
        grid-column: 1 / -1;
    }

    .form-group label,
    .checkbox-line {
        display: block;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 7px;
    }

    .form-control {
        border: 1px solid #cfd6e3;
        border-radius: 6px;
        color: #172033;
        font: inherit;
        min-height: 42px;
        padding: 9px 11px;
        width: 100%;
    }

    textarea.form-control {
        min-height: 128px;
        resize: vertical;
    }

    .rich-editor {
        border: 1px solid #cfd6e3;
        border-radius: 6px;
        overflow: hidden;
    }

    .rich-editor__toolbar {
        align-items: center;
        background: #f5f7fb;
        border-bottom: 1px solid #d8deea;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        padding: 8px;
    }

    .rich-editor__button {
        align-items: center;
        background: white;
        border: 1px solid #d8deea;
        border-radius: 6px;
        color: #172033;
        cursor: pointer;
        display: inline-flex;
        font: inherit;
        font-size: 14px;
        font-weight: 700;
        height: 34px;
        justify-content: center;
        min-width: 34px;
        padding: 0 9px;
    }

    .rich-editor__button:hover {
        background: #eef2ff;
        border-color: #b8c2ff;
        color: #2447f9;
    }

    .rich-editor__surface {
        background: white;
        color: #172033;
        line-height: 1.65;
        min-height: 180px;
        outline: none;
        padding: 14px;
    }

    .rich-editor__surface:focus {
        box-shadow: inset 0 0 0 2px rgba(36, 71, 249, 0.22);
    }

    .rich-editor__surface p,
    .rich-editor__surface ul,
    .rich-editor__surface ol,
    .rich-editor__surface blockquote,
    .rich-editor__surface h3 {
        margin: 0 0 12px;
    }

    .rich-editor__surface ul,
    .rich-editor__surface ol {
        padding-left: 22px;
    }

    .rich-editor__surface blockquote {
        border-left: 3px solid #2447f9;
        color: #526071;
        padding-left: 12px;
    }

    .rich-editor__surface a {
        color: #2447f9;
        font-weight: 700;
    }

    .rich-editor__input {
        display: none;
    }

    .paragraphs-header {
        border-top: 1px solid #e6e8ef;
        margin-top: 28px;
        padding-top: 24px;
    }

    .paragraphs-header h2,
    .paragraph-card h3 {
        margin: 0 0 6px;
    }

    .paragraphs-header p {
        color: #526071;
        margin: 0;
    }

    .paragraph-stack {
        display: grid;
        gap: 16px;
        margin-top: 18px;
    }

    .paragraph-card {
        border: 1px solid #d8deea;
        border-radius: 8px;
        padding: 18px;
    }

    .paragraph-card__header {
        margin-bottom: 14px;
    }

    .current-image {
        align-items: center;
        display: flex;
        gap: 14px;
        margin-top: 10px;
    }

    .current-image img {
        border-radius: 6px;
        height: 72px;
        object-fit: cover;
        width: 112px;
    }

    .button-row {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .button {
        align-items: center;
        background: #2447f9;
        border: 1px solid #2447f9;
        border-radius: 6px;
        color: white;
        cursor: pointer;
        display: inline-flex;
        font: inherit;
        font-weight: 700;
        min-height: 42px;
        padding: 9px 14px;
        text-decoration: none;
    }

    .button--secondary {
        background: white;
        color: #172033;
        border-color: #cfd6e3;
    }

    .alert {
        border-radius: 6px;
        margin-bottom: 18px;
        padding: 12px 14px;
    }

    .alert-success {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    .alert-danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    .pagination-wrap {
        padding-top: 18px;
    }

    html.universe-theme-dark .rich-editor {
        border-color: #2f3c52;
    }

    html.universe-theme-dark .rich-editor__toolbar {
        background: #111827;
        border-color: #2f3c52;
    }

    html.universe-theme-dark .rich-editor__button {
        background: #172033;
        border-color: #2f3c52;
        color: #e5e7eb;
    }

    html.universe-theme-dark .rich-editor__button:hover {
        background: #22304a;
        border-color: #5c6fff;
        color: #c7d2fe;
    }

    html.universe-theme-dark .rich-editor__surface {
        background: #172033;
        color: #e5e7eb;
    }

    html.universe-theme-dark .rich-editor__surface blockquote {
        color: #a9b4c6;
    }

    html.universe-theme-dark .rich-editor__surface a {
        color: #8ea0ff;
    }

    @media (max-width: 720px) {
        .admin-header,
        .button-row {
            flex-direction: column;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .admin-table,
        .admin-table tbody,
        .admin-table tr,
        .admin-table td {
            display: block;
            width: 100%;
        }

        .admin-table thead {
            display: none;
        }

        .admin-table tr {
            border-bottom: 1px solid #edf0f5;
            padding: 14px 0;
        }

        .admin-table td {
            border-bottom: 0;
            padding: 7px 0;
        }

        .actions-cell {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>
