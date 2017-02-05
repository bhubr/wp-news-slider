<?php

use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    public function test_close_and_strip_tags_close_only()
    {
        $html = '<html><head></head><body><div>';
        $processed = close_and_strip_tags($html);
        $this->assertEquals('<div></div>', $processed);
    }

    public function test_get_autoclosing_tags()
    {
      $html = '<html><head></head><body><div><span>Blah</span><img src="" /> <br/><input value="toto" /></div></body></html>';
      $autoclosing_tags = get_autoclosing_tags($html);
      $this->assertEquals(['img', 'br', 'input'], $autoclosing_tags);
    }
/*
    public function test_get_html_without_autoclosing_tags() {
      $html = '<html><head></head><body><div><span>Blah</span><img src="" /> <br/><input value="toto" /></div></body></html>';
      $stripped = get_html_without_autoclosing_tags($html);
      $this->assertEquals('<html><head></head><body><div><span>Blah</span> </div></body></html>', $stripped);
    }

    public function test_close_non_autoclosing_tags() {
      $html = '<html><head></head><body><div><span>Blah</span>';
      $closed = close_non_autoclosing_tags($html);
      $this->assertEquals('<html><head></head><body><div><span>Blah</span></div></body></html>', $closed);
    }*/

    public function test_close_and_strip_tags_strip_only()
    {
        $html = '<html><head></head><body><div><span>Blah</span><img src="" /> <form><input value="toto" /></form></div></body></html>';
        $processed = close_and_strip_tags($html, ['img', 'input', 'form']);
        $this->assertEquals('<div><span>Blah</span></div>', $processed);
    }
}
?>