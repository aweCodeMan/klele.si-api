<?php

namespace App\Reactors;

use App\Models\User;
use App\StorableEvents\UserRegisteredEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class UserReactor extends Reactor implements ShouldQueue
{
    public function onUserRegistered(UserRegisteredEvent $event)
    {
        User::where('uuid', $event->aggregateRootUuid())->first()->sendEmailVerificationNotification();
    }
}
