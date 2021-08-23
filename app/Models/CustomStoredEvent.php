<?php


namespace App\Models;


use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;

class CustomStoredEvent extends EloquentStoredEvent
{

    public static function boot()
    {
        parent::boot();

        static::creating(function (CustomStoredEvent $storedEvent) {
            if (auth()->user()) {
                $storedEvent->meta_data['user-uuid'] = auth()->user()->uuid;
            }
        });
    }
}
