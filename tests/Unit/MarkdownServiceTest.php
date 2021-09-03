<?php

namespace Tests\Unit;

use App\Services\CustomParsedown;
use App\Services\MarkdownService;
use PHPUnit\Framework\TestCase;

class MarkdownServiceTest extends TestCase
{
    /** @test */
    public function it_adds_link_attributes()
    {
        $this->assertSame('<p><a href="https://example.com" target="_blank" rel="noreferrer noopener">example.com</a></p>', MarkdownService::parse('[example.com](https://example.com)'));
    }
}
