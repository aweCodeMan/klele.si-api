<?php

namespace App\StorableEvents;

use App\StorableEvents\Data\LinkPostUpdatedData;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LinkPostUpdated extends ShouldBeStored
{
    public function __construct(public LinkPostUpdatedData $data)
    {
    }
}
