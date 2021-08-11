<?php

namespace App\StorableEvents\Data;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CommentCreatedData extends ShouldBeStored
{
    public function __construct(public string $author_uuid, public string $root_uuid, public ?string $parent_uuid, public string $markdown)
    {
    }
}
