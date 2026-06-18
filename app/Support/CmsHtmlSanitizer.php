<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class CmsHtmlSanitizer
{
    /**
     * Tags allowed in CMS paragraph bodies.
     *
     * @var array<int, string>
     */
    private const ALLOWED_TAGS = [
        'a',
        'blockquote',
        'br',
        'em',
        'h3',
        'h4',
        'i',
        'li',
        'ol',
        'p',
        'strong',
        'u',
        'ul',
    ];

    /**
     * Attributes allowed per tag.
     *
     * @var array<string, array<int, string>>
     */
    private const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'rel', 'target', 'title'],
    ];

    /**
     * Sanitize CMS editor HTML for safe public rendering.
     */
    public static function sanitize(?string $html): string
    {
        $html = self::normalizeText((string) $html);

        if ($html === '') {
            return '';
        }

        if (! class_exists(DOMDocument::class)) {
            return strip_tags($html, '<'.implode('><', self::ALLOWED_TAGS).'>');
        }

        $document = new DOMDocument('1.0', 'UTF-8');

        libxml_use_internal_errors(true);
        $document->loadHTML(
            '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body><div id="cms-fragment">'.$html.'</div></body></html>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $fragment = $document->getElementById('cms-fragment');

        if (! $fragment instanceof DOMElement) {
            return '';
        }

        self::sanitizeNode($fragment);

        $cleanHtml = '';

        foreach ($fragment->childNodes as $childNode) {
            $cleanHtml .= $document->saveHTML($childNode);
        }

        return self::normalizeText($cleanHtml);
    }

    /**
     * Normalize editor whitespace and common UTF-8 mojibake artifacts.
     */
    private static function normalizeText(string $text): string
    {
        $text = str_replace(['&nbsp;', "\xc2\xa0", 'Â ', 'Â ', 'ÃƒÂ‚', 'ÃÂÂ ', 'ÃÂÂ'], ' ', $text);
        $text = preg_replace('/\x{00C3}\x{0083}\x{00C2}\x{0082}\x{00C2}?/u', ' ', $text) ?? $text;
        $text = preg_replace('/[ \t]{2,}/', ' ', $text) ?? $text;

        return trim($text);
    }

    /**
     * Recursively sanitize a DOM node.
     */
    private static function sanitizeNode(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $childNode) {
            if (! $childNode instanceof DOMElement) {
                continue;
            }

            $tagName = strtolower($childNode->tagName);

            if (in_array($tagName, ['script', 'style'], true)) {
                $childNode->parentNode?->removeChild($childNode);

                continue;
            }

            if (! in_array($tagName, self::ALLOWED_TAGS, true)) {
                self::sanitizeNode($childNode);
                self::unwrapNode($childNode);

                continue;
            }

            self::sanitizeAttributes($childNode, $tagName);
            self::sanitizeNode($childNode);
        }
    }

    /**
     * Remove disallowed attributes from an allowed element.
     */
    private static function sanitizeAttributes(DOMElement $element, string $tagName): void
    {
        $allowedAttributes = self::ALLOWED_ATTRIBUTES[$tagName] ?? [];

        foreach (iterator_to_array($element->attributes) as $attribute) {
            if (! in_array($attribute->name, $allowedAttributes, true)) {
                $element->removeAttribute($attribute->name);
            }
        }

        if ($tagName !== 'a') {
            return;
        }

        $href = trim($element->getAttribute('href'));

        if ($href === '' || ! self::isAllowedHref($href)) {
            $element->removeAttribute('href');
        }

        if ($element->getAttribute('target') === '_blank') {
            $element->setAttribute('rel', 'noopener noreferrer');
        }
    }

    /**
     * Determine whether a link target can be rendered publicly.
     */
    private static function isAllowedHref(string $href): bool
    {
        if (str_starts_with($href, '#') || str_starts_with($href, '/')) {
            return true;
        }

        $scheme = parse_url($href, PHP_URL_SCHEME);

        return $scheme === null || in_array(strtolower($scheme), ['http', 'https', 'mailto', 'tel'], true);
    }

    /**
     * Replace an unsupported element with its children.
     */
    private static function unwrapNode(DOMElement $element): void
    {
        $parent = $element->parentNode;

        if ($parent === null) {
            return;
        }

        while ($element->firstChild !== null) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }
}
