<?php

namespace App\StorableEvents;

use App\StorableEvents\Data\PostPinnedEventData;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PostLocked extends ShouldBeStored
{
    public function __construct()
    {
    }
}
