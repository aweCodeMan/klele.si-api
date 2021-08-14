<?php

namespace App\StorableEvents;

use App\StorableEvents\Data\VoteSubmittedEventData;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class VoteSubmitted extends ShouldBeStored
{
    public function __construct(public VoteSubmittedEventData $data)
    {
    }
}
