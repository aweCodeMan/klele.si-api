<?php

namespace App\StorableEvents\Data;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class MarkdownPostCreatedData extends ShouldBeStored
{
    public function __construct(public string $author_uuid, public string $title, public string $group_uuid, public string $markdown)
    {
    }
}
