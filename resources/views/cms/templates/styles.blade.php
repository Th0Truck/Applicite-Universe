<style>
    body.cms-page {
        background: #f6f7fb;
        color: #172033;
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        margin: 0;
    }

    .cms-shell {
        margin: 0 auto;
        max-width: 920px;
        padding: 56px 20px;
    }

    .cms-shell--feature {
        max-width: 1120px;
    }

    .cms-hero {
        margin-bottom: 34px;
    }

    .cms-hero h1 {
        font-size: 42px;
        margin: 0;
    }

    .cms-content-stack {
        display: grid;
        gap: 22px;
    }

    .cms-block,
    .cms-feature-row {
        background: white;
        border: 1px solid #e6e8ef;
        border-radius: 8px;
        box-shadow: 0 8px 22px rgba(20, 24, 40, 0.08);
        padding: 26px;
    }

    .cms-block img,
    .cms-feature-row img {
        border-radius: 8px;
        height: auto;
        margin-bottom: 20px;
        width: 100%;
    }

    .cms-kicker {
        color: #2447f9;
        font-size: 14px;
        font-weight: 800;
        margin: 0 0 8px;
        text-transform: uppercase;
    }

    .cms-block h2,
    .cms-feature-row h2 {
        font-size: 26px;
        margin: 0 0 10px;
    }

    .cms-block p,
    .cms-feature-row p {
        color: #526071;
        line-height: 1.7;
    }

    .cms-feature-row {
        align-items: center;
        display: grid;
        gap: 28px;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        margin-bottom: 24px;
    }

    .cms-feature-row--reverse img {
        order: 2;
    }

    .cms-feature-row img {
        aspect-ratio: 4 / 3;
        margin-bottom: 0;
        object-fit: cover;
    }

    @media (max-width: 760px) {
        .cms-hero h1 {
            font-size: 32px;
        }

        .cms-feature-row {
            grid-template-columns: 1fr;
        }

        .cms-feature-row--reverse img {
            order: 0;
        }
    }
</style>
