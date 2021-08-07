<?php

namespace App\StorableEvents;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserRegisteredEvent extends ShouldBeStored
{
    public function __construct(public Data\UserRegisteredEventData $data)
    {
    }
}

