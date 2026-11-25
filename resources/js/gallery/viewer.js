/**
 * Open gallery images in a native dialog and restore focus when it closes.
 *
 * @param {HTMLElement} root
 * @param {HTMLDialogElement|null} viewer
 * @returns {void}
 */
export function initializeViewer(root, viewer) {
    const viewerImage = viewer?.querySelector('.gallery-viewer__image');

    if (!viewerImage || typeof viewer.showModal !== 'function') {
        return;
    }

    let opener = null;

    for (const link of root.querySelectorAll('.image-hover-frame')) {
        link.addEventListener('click', (event) => {
            // Preserve standard browser behavior for opening links in another tab.
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button !== 0) {
                return;
            }

            event.preventDefault();
            opener = link;
            viewerImage.alt = link.querySelector('img')?.alt ?? '';
            viewerImage.src = link.href;
            viewer.showModal();
            root.ownerDocument.documentElement.classList.add('gallery-viewer-open');
        });
    }

    // Both the close button and Escape trigger the dialog's native close event.
    viewer.addEventListener('close', () => {
        root.ownerDocument.documentElement.classList.remove('gallery-viewer-open');
        viewerImage.removeAttribute('src');
        viewerImage.alt = '';
        opener?.focus({ preventScroll: true });
        opener = null;
    });
}
