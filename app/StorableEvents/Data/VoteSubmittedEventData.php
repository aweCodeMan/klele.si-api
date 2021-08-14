<?php

namespace App\StorableEvents\Data;

class VoteSubmittedEventData
{
    public function __construct(public string $user_uuid, public mixed $vote, public string $type)
    {
    }
}
