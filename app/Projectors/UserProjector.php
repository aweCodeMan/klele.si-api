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
            'nickname' => $event->data->nickname,
            'email' => $event->data->email,
            'full_name' => $this->getFullName($event),
            'password' => Hash::make(Str::random()), // We fake a random password as we don't have the password stored in the event
        ]);
    }

    public function onUserUpdated(UserUpdatedEvent $event)
    {
        User::where('uuid', $event->aggregateRootUuid())->update([
            'name' => $event->data->name,
            'surname' => $event->data->surname,
            'nickname' => $event->data->nickname,
            'full_name' => $this->getFullName($event),
        ]);
    }

    private function getFullName($event)
    {
        $names = [];

        if ($event->data->name) {
            $names[] = $event->data->name;
        }

        if ($event->data->surname) {
            $names[] = $event->data->surname;
        }

        return implode(" ", $names);
    }
}
