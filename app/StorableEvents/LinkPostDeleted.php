<?php

namespace App\StorableEvents;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LinkPostDeleted extends ShouldBeStored
{
    public function __construct()
    {
    }
}
