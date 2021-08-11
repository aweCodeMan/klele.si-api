<?php

namespace App\StorableEvents;

use App\StorableEvents\Data\MarkdownPostUpdatedData;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class MarkdownPostUpdated extends ShouldBeStored
{
    public function __construct(public MarkdownPostUpdatedData $data)
    {
    }
}
