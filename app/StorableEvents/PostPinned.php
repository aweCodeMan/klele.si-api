<?php

namespace App\StorableEvents;

use App\StorableEvents\Data\PostPinnedEventData;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PostPinned extends ShouldBeStored
{
    public function __construct(public PostPinnedEventData $data)
    {
    }
}
