<?php

namespace App\StorableEvents;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CommentDeleted extends ShouldBeStored
{
    public function __construct()
    {
    }
}
