<?php

use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    public function test_close_and_strip_tags_close_only()
    {
        $html = '<html><head></head><body><div>';
        $processed = close_and_strip_tags($html);
        $this->assertEquals('<html><head></head><body><div></div></body></html>', $processed);
    }

    public function test_close_and_strip_tags_strip_only()
    {
        $html = '<html><head></head><body><div><span>Blah</span><img src="" /> <input value="toto" /></div></body></html>';
        $processed = close_and_strip_tags($html, ['img', 'input', 'span']);
        $this->assertEquals('<html><head></head><body><div></div></body></html>', $processed);
    }
}
?>