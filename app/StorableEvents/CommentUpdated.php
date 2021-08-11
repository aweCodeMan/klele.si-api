<?php

namespace App\StorableEvents;

use App\StorableEvents\Data\CommentUpdatedData;
use App\StorableEvents\Data\LinkPostCreatedData;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CommentUpdated extends ShouldBeStored
{
    public function __construct(public CommentUpdatedData $data)
    {
    }
}
