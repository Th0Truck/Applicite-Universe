import test from 'node:test';
import assert from 'node:assert/strict';
import { nearestEdge, initializeHover } from '../../resources/js/gallery/hover.js';
import { initializeViewer } from '../../resources/js/gallery/viewer.js';

const bounds = { top: 100, bottom: 300, left: 50, right: 350 };

for (const [x, y, expected] of [
    [200, 101, 'oppe'], [200, 299, 'nede'], [51, 200, 'venstre'], [349, 200, 'højre'],
]) {
    test(`detects ${expected} with viewport offsets`, () => {
        assert.equal(nearestEdge(bounds, x, y), expected);
    });
}

test('interrupted hover exits through the center without a diagonal segment', () => {
    const events = {};
    const animations = [];
    const preference = { matches: false };
    const box = {
        style: {},
        animate(frames) {
            const animation = { playState: 'running', cancel() {} };
            animations.push({ frames, animation });
            return animation;
        },
    };
    const border = {
        querySelector: () => box,
        getBoundingClientRect: () => bounds,
        addEventListener: (name, handler) => { events[name] = handler; },
    };
    initializeHover({
        querySelectorAll: () => [border],
        ownerDocument: { defaultView: {
            matchMedia: () => preference,
            getComputedStyle: () => ({ transform: 'matrix(1, 0, 0, 1, -80, 0)' }),
        } },
    });
    events.pointerenter({ pointerType: 'mouse', clientX: 51, clientY: 200 });
    assert.equal(animations[0].frames[0].transform, 'translateX(-100%)');
    events.pointerleave({ pointerType: 'mouse', clientX: 200, clientY: 99 });
    assert.deepEqual(animations[1].frames.map(({ transform }) => transform), [
        'matrix(1, 0, 0, 1, -80, 0)', 'translate(0, 0)', 'translateY(-100%)',
    ]);
    preference.matches = true;
    events.pointerenter({ pointerType: 'mouse', clientX: 51, clientY: 200 });
    assert.equal(animations.length, 2);
    assert.equal(box.style.transform, 'translate(0, 0)');
    events.pointerenter({ pointerType: 'touch', clientX: 51, clientY: 200 });
    assert.equal(animations.length, 2);
});

test('viewer opens, cleans up on close, restores focus and preserves modified clicks', () => {
    const events = {};
    const classes = new Set();
    const image = { removeAttribute(key) { delete this[key]; } };
    const link = {
        href: '/storage/photo.jpg',
        querySelector: () => ({ alt: 'Landscape' }),
        addEventListener: (name, handler) => { events[name] = handler; },
        focus: () => { events.focusRestored = true; },
    };
    const viewer = {
        querySelector: () => image,
        showModal: () => { events.opened = true; },
        addEventListener: (name, handler) => { events[name] = handler; },
    };
    initializeViewer({
        querySelectorAll: () => [link],
        ownerDocument: { documentElement: { classList: {
            add: (name) => classes.add(name), remove: (name) => classes.delete(name),
        } } },
    }, viewer);
    events.click({ button: 0, preventDefault() { events.prevented = true; } });
    assert.equal(events.opened, true);
    assert.equal(events.prevented, true);
    assert.equal(image.src, link.href);
    assert.equal(image.alt, 'Landscape');
    assert.equal(classes.has('gallery-viewer-open'), true);
    events.close();
    assert.equal(classes.size, 0);
    assert.equal(image.src, undefined);
    assert.equal(events.focusRestored, true);
    events.opened = false;
    for (const modifier of ['ctrlKey', 'metaKey', 'shiftKey', 'altKey']) {
        events.click({ button: 0, [modifier]: true });
        assert.equal(events.opened, false);
    }
});

test('missing or unsupported dialog leaves image links usable', () => {
    const root = { querySelectorAll() { assert.fail('Links should not be intercepted'); } };
    initializeViewer(root, null);
    initializeViewer(root, { querySelector: () => ({}) });
});
