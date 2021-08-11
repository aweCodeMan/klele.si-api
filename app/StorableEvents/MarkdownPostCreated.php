<?php

namespace App\StorableEvents;

use App\StorableEvents\Data\MarkdownPostCreatedData;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class MarkdownPostCreated extends ShouldBeStored
{
    public function __construct(public MarkdownPostCreatedData $data)
    {
    }
}
