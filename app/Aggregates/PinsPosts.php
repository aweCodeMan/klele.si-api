<?php

namespace App\Aggregates;

use App\StorableEvents\Data\PostPinnedEventData;
use App\StorableEvents\PostPinned;

trait PinsPosts
{
    public function pin(string $pinnedAt, ?string $pinnedUntil = null)
    {
        $this->recordThat(new PostPinned(new PostPinnedEventData($pinnedAt, $pinnedUntil)));
        return $this;
    }
}
