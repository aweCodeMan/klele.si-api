<?php


namespace App\StorableEvents\Data;


class MarkdownPostUpdatedData
{
    public function __construct(public string $title, public string $markdown)
    {
    }
}
