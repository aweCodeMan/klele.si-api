<?php

namespace App\StorableEvents;

use App\StorableEvents\Data\LinkPostCreatedData;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LinkPostCreated extends ShouldBeStored
{
    public function __construct(public LinkPostCreatedData $data)
    {
    }
}
