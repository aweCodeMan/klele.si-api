<?php


namespace App\StorableEvents\Data;


class CommentUpdatedData
{
    public function __construct(public string $markdown)
    {
    }
}
