<?php

namespace App\Aggregates;

use App\StorableEvents\Data\VoteSubmittedEventData;
use App\StorableEvents\VoteSubmitted;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class VoteAggregate extends AggregateRoot
{
    public function store(string $user_uuid, mixed $vote, string $type)
    {
        $this->recordThat(new VoteSubmitted(new VoteSubmittedEventData($user_uuid, $vote, $type)));
        return $this;
    }
}
