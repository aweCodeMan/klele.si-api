<?php

namespace App\StorableEvents;

use App\StorableEvents\Data\PostPinnedEventData;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CommentLocked extends ShouldBeStored
{
    public function __construct()
    {
    }
}
