/**
 * Keep page content clear of the fixed footer when text wraps or fonts load.
 */
const footer = document.querySelector('.cms-footer');

if (footer) {
    const updateFooterHeight = () => {
        document.documentElement.style.setProperty(
            '--site-footer-height',
            `${footer.getBoundingClientRect().height}px`,
        );
    };

    updateFooterHeight();
    new ResizeObserver(updateFooterHeight).observe(footer);
}
