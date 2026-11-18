import { initializeHover } from './gallery/hover.js';
import { initializeViewer } from './gallery/viewer.js';

const gallery = document.querySelector('.gallery');

if (gallery) {
    initializeHover(gallery);
    initializeViewer(gallery, document.querySelector('.gallery-viewer'));
}
