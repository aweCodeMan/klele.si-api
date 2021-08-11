<?php

namespace App\StorableEvents;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class MarkdownPostDeleted extends ShouldBeStored
{
    public function __construct()
    {
    }
}
