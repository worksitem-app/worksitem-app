<?php

namespace Tests\Unit;

use App\Support\SafeHtml;
use PHPUnit\Framework\TestCase;

class SafeHtmlTest extends TestCase
{
    public function test_izinli_etiketler_kalir(): void
    {
        $this->assertSame('<strong>Merhaba</strong><br><ul><li>a</li></ul>', SafeHtml::clean('<strong>Merhaba</strong><br><ul><li>a</li></ul>'));
    }

    public function test_script_ve_event_handler_temizlenir(): void
    {
        $dirty = '<p onclick="steal()">x</p><script>alert(1)</script><img src=x onerror=alert(1)><a href="javascript:alert(1)">y</a>';
        $clean = SafeHtml::clean($dirty);

        $this->assertStringNotContainsString('script', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringNotContainsString('<img', $clean);
        $this->assertStringNotContainsString('<a', $clean);
        $this->assertSame('<p>x</p>y', $clean);
    }
}
