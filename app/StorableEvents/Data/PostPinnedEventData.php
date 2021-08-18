<?php

namespace App\StorableEvents\Data;

class PostPinnedEventData
{
    public function __construct(public string $pinned_at, public ?string $pinned_until = null)
    {
    }
}
