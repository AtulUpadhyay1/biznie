<?php

namespace App\Support;

/**
 * Turns admin-written news (pasted reports full of emoji, "⸻" rules and the
 * odd HTML) into a clean excerpt for cards and a safe body for the article
 * page (B19).
 */
final class NewsText
{
    /** Emoji, pictographs, dingbats, keycaps and the joiners between them. */
    private const EMOJI = '/[\x{1F000}-\x{1FAFF}\x{2600}-\x{27BF}\x{2B00}-\x{2BFF}\x{2300}-\x{23FF}\x{FE0E}\x{FE0F}\x{200D}\x{20E3}\x{E0020}-\x{E007F}]/u';

    /** Decorative rules: ⸻ ⸺ and runs of box-drawing lines, dashes or underscores. */
    private const RULES = '/[\x{2E3A}\x{2E3B}]+|[\x{2500}-\x{257F}]{2,}|[\x{2014}\-_=*~]{3,}/u';

    private const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><ul><ol><li><h2><h3><h4><blockquote><a><table><thead><tbody><tr><th><td><hr>';

    /** Plain text for a card: no tags, emoji or rules; cut at a word. */
    public static function excerpt(?string $body, int $limit = 220): string
    {
        $text = html_entity_decode(strip_tags(preg_replace('/<(br|\/p|\/li|\/h\d)\s*\/?>/i', ' ', (string) $body)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace([self::EMOJI, self::RULES, '/[\x{200B}\x{00A0}]/u'], ' ', $text);
        // A bullet or colon left alone once its emoji went.
        $text = preg_replace('/\s+([:,.;])/u', '$1', $text);
        $text = trim(preg_replace('/\s+/u', ' ', $text));

        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        $cut = mb_substr($text, 0, $limit);
        $space = mb_strrpos($cut, ' ');

        return rtrim($space > $limit * 0.6 ? mb_substr($cut, 0, $space) : $cut, " ,;:–-") . '…';
    }

    /**
     * The article body as safe HTML. Plain-text posts become paragraphs; a
     * "⸻" line becomes a divider. Only formatting tags survive, with no
     * event handlers, inline styles or javascript: links.
     */
    public static function bodyHtml(?string $body): string
    {
        $body = trim((string) $body);
        if ($body === '') {
            return '';
        }

        // Real markup only: a pasted "<grade>" in a plain-text post is text.
        if (! preg_match('/<\/?(p|br|div|span|strong|b|em|i|u|ul|ol|li|h[1-6]|a|table|tr|td|th|blockquote|img|hr)\b[^>]*>/i', $body)) {
            $paragraphs = preg_split('/\n\s*\n/u', str_replace("\r\n", "\n", $body)) ?: [];
            $body = implode('', array_map(function ($p) {
                $p = trim($p);
                if (preg_match('/^(' . trim(self::RULES, '/u') . ')$/u', $p)) {
                    return '<hr>';
                }

                return $p === '' ? '' : '<p>' . nl2br(e($p), false) . '</p>';
            }, $paragraphs));
        }

        $body = preg_replace('#<(script|style|iframe|object|embed)\b[^>]*>.*?</\1>#is', '', $body);
        $body = strip_tags($body, self::ALLOWED_TAGS);
        // No handlers or inline styles; links must be http(s) or mailto.
        $body = preg_replace('/\s(on\w+|style)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $body);
        $body = preg_replace('/\shref\s*=\s*("|\')\s*(?!https?:|mailto:|\/)[^"\']*\1/i', ' href="#"', $body);
        $body = preg_replace('/<a\b(?![^>]*\brel=)/i', '<a rel="noopener noreferrer" target="_blank"', $body);
        $body = preg_replace('/<p>\s*(' . trim(self::RULES, '/u') . ')\s*<\/p>/u', '<hr>', $body);

        return $body;
    }

    /** ~200 words a minute, at least one. */
    public static function readingMinutes(?string $body): int
    {
        $words = str_word_count(strip_tags((string) $body));

        return max(1, (int) round($words / 200));
    }
}
