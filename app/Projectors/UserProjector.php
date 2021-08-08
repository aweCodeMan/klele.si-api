<?php

namespace App\Projectors;

use App\Models\User;
use App\StorableEvents\UserRegisteredEvent;
use App\StorableEvents\UserUpdatedEvent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserProjector extends Projector
{
    public function onUserRegistered(UserRegisteredEvent $event)
    {
        User::create([
            'uuid' => $event->aggregateRootUuid(),
            'name' => $event->data->name,
            'surname' => $event->data->surname,
            'email' => $event->data->email,
            'full_name' => "{$event->data->name} {$event->data->surname}",
            'password' => Hash::make(Str::random()), // We fake a random password as we don't have the password stored in the event
        ]);
    }

    public function onUserUpdated(UserUpdatedEvent $event)
    {
        User::where('uuid', $event->aggregateRootUuid())->update([
            'name' => $event->data->name,
            'surname' => $event->data->surname,
            'full_name' => "{$event->data->name} {$event->data->surname}",
        ]);
    }
}
