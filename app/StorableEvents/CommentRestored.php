<?php

namespace App\StorableEvents;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CommentRestored extends ShouldBeStored
{
    public function __construct()
    {
    }
}
