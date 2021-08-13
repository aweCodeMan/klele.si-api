<?php

namespace App\Aggregates;

use App\StorableEvents\Data\UserRegisteredEventData;
use App\StorableEvents\Data\UserUpdatedEventData;
use App\StorableEvents\UserRegisteredEvent;
use App\StorableEvents\UserUpdatedEvent;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class UserAggregate extends AggregateRoot
{
    public function register(?string $name, ?string $surname, string $nickname, string $email): static
    {
        $this->recordThat(new UserRegisteredEvent(new UserRegisteredEventData($name, $surname, $nickname, $email)));
        return $this;
    }

    public function update(?string $name, ?string $surname, string $nickname): static
    {
        $this->recordThat(new UserUpdatedEvent(new UserUpdatedEventData($name, $surname, $nickname)));
        return $this;
    }
}
