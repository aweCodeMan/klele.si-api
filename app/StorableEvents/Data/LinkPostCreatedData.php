<?php

namespace App\StorableEvents\Data;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LinkPostCreatedData extends ShouldBeStored
{
    public function __construct(public string $author_uuid, public string $title, public string $group_uuid, public string $link)
    {
    }
}
