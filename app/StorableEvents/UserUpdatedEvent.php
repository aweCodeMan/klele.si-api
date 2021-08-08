<?php

namespace App\StorableEvents;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserUpdatedEvent extends ShouldBeStored
{
    public function __construct(public Data\UserUpdatedEventData $data)
    {
    }
}

