<?php

namespace App\StorableEvents;

use App\StorableEvents\Data\CommentCreatedData;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CommentCreated extends ShouldBeStored
{
    public function __construct(public CommentCreatedData $data)
    {
    }
}
