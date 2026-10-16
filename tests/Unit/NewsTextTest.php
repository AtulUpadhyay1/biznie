<?php

namespace Tests\Unit;

use App\Support\NewsText;
use PHPUnit\Framework\TestCase;

/** News cards and article bodies (B19). */
class NewsTextTest extends TestCase
{
    public function test_excerpt_drops_emoji_rules_and_tags(): void
    {
        $body = "📊 Steel Market Update – 21st June 2025\n📉 Market Mood: Weak\n\n⸻\n\n🔍 <b>Overview</b>:\nPrices &amp; demand fell.";

        $this->assertSame(
            'Steel Market Update – 21st June 2025 Market Mood: Weak Overview: Prices & demand fell.',
            NewsText::excerpt($body)
        );
    }

    public function test_excerpt_is_cut_at_a_word(): void
    {
        $excerpt = NewsText::excerpt(str_repeat('steel market ', 40), 50);

        $this->assertLessThanOrEqual(51, mb_strlen($excerpt));
        $this->assertStringEndsWith('…', $excerpt);
        $this->assertStringNotContainsString('marke…', $excerpt);
    }

    public function test_plain_text_body_becomes_paragraphs_with_dividers(): void
    {
        $html = NewsText::bodyHtml("First <para>\nline two\n\n⸻\n\nSecond");

        $this->assertSame('<p>First &lt;para&gt;<br>' . "\n" . 'line two</p><hr><p>Second</p>', $html);
    }

    public function test_html_body_is_sanitised(): void
    {
        $html = NewsText::bodyHtml('<p onclick="x()" style="color:red">Hi</p><script>alert(1)</script><a href="javascript:alert(1)">x</a><img src=x onerror=alert(1)>');

        $this->assertStringNotContainsString('script', $html);
        $this->assertStringNotContainsString('onclick', $html);
        $this->assertStringNotContainsString('style=', $html);
        $this->assertStringNotContainsString('javascript:', $html);
        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringContainsString('<p>Hi</p>', $html);
    }
}
