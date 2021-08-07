<?php

namespace App\Aggregates;

use App\StorableEvents\Data\UserRegisteredEventData;
use App\StorableEvents\UserRegisteredEvent;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class UserAggregate extends AggregateRoot
{
    public function register(string $name, string $surname, string $email): static
    {
        $this->recordThat(new UserRegisteredEvent(new UserRegisteredEventData($name, $surname, $email)));
        return $this;
    }
}
