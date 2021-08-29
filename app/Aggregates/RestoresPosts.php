<?php

namespace App\Aggregates;

use App\StorableEvents\Data\PostPinnedEventData;
use App\StorableEvents\PostPinned;
use App\StorableEvents\PostRestored;

trait RestoresPosts
{
    public function restore()
    {
        $this->recordThat(new PostRestored());
        return $this;
    }
}
