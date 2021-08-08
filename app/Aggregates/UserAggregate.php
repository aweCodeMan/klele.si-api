<?php

namespace App\Aggregates;

use App\StorableEvents\Data\UserRegisteredEventData;
use App\StorableEvents\Data\UserUpdatedEventData;
use App\StorableEvents\UserRegisteredEvent;
use App\StorableEvents\UserUpdatedEvent;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class UserAggregate extends AggregateRoot
{
    public function register(string $name, string $surname, string $email): static
    {
        $this->recordThat(new UserRegisteredEvent(new UserRegisteredEventData($name, $surname, $email)));
        return $this;
    }

    public function update(string $name, string $surname): static
    {
        $this->recordThat(new UserUpdatedEvent(new UserUpdatedEventData($name, $surname)));
        return $this;
    }
}
