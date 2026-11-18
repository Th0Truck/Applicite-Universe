/**
 * Find the edge nearest to the pointer, using viewport coordinates.
 * Ties at corners prefer the horizontal edge (top or bottom).
 *
 * @param {DOMRect} bounds
 * @param {number} clientX
 * @param {number} clientY
 * @returns {'oppe'|'nede'|'venstre'|'højre'}
 */
function nearestEdge(bounds, clientX, clientY) {
    const distances = [
        ['oppe', Math.abs(clientY - bounds.top)],
        ['nede', Math.abs(bounds.bottom - clientY)],
        ['venstre', Math.abs(clientX - bounds.left)],
        ['højre', Math.abs(bounds.right - clientX)],
    ];

    return distances.reduce((nearest, edge) => edge[1] < nearest[1] ? edge : nearest)[0];
}

/**
 * Move the overlay completely outside the image along a single axis.
 *
 * @param {'oppe'|'nede'|'venstre'|'højre'} edge
 * @returns {string}
 */
function edgeTransform(edge) {
    return {
        oppe: 'translateY(-100%)',
        nede: 'translateY(100%)',
        venstre: 'translateX(-100%)',
        højre: 'translateX(100%)',
    }[edge];
}

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

for (const border of document.querySelectorAll('.gallery .image-hover-border')) {
    const box = border.querySelector('.image-hover-box');

    if (!box) {
        continue;
    }

    let animation = null;
    let previousEdge = null;

    /**
     * Replace the current animation and retain its destination after completion.
     *
     * @param {string[]} transforms
     * @returns {void}
     */
    const slide = (transforms) => {
        animation?.cancel();
        box.style.transform = transforms.at(-1);
        box.style.opacity = '1';
        animation = reducedMotion.matches ? null : box.animate(
            transforms.map((transform) => ({ transform })),
            { duration: transforms.length === 3 ? 360 : 240, easing: 'ease-out' },
        );
    };

    /**
     * Report the closest edge without logging every pointer movement.
     *
     * @param {PointerEvent} event
     * @returns {'oppe'|'nede'|'venstre'|'højre'}
     */
    const logEdge = (event) => {
        const edge = nearestEdge(border.getBoundingClientRect(), event.clientX, event.clientY);

        if (edge !== previousEdge) {
            console.log('Gallery border:', edge);
            previousEdge = edge;
        }

        return edge;
    };

    border.addEventListener('pointerenter', (event) => {
        if (event.pointerType === 'mouse') {
            slide([edgeTransform(logEdge(event)), 'translate(0, 0)']);
        }
    });

    border.addEventListener('pointermove', (event) => {
        if (event.pointerType === 'mouse') {
            logEdge(event);
        }
    });

    border.addEventListener('pointerleave', (event) => {
        if (event.pointerType !== 'mouse') {
            return;
        }

        const edge = logEdge(event);
        const currentTransform = getComputedStyle(box).transform;

        // Finish an interrupted entry along its axis before exiting along the new axis.
        // Interpolating directly between different axes would produce diagonal motion.
        const transforms = animation?.playState === 'running'
            ? [currentTransform, 'translate(0, 0)', edgeTransform(edge)]
            : ['translate(0, 0)', edgeTransform(edge)];

        slide(transforms);
        previousEdge = null;
    });
}
