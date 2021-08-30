<?php

namespace App\Aggregates;

use App\StorableEvents\PostLocked;
use App\StorableEvents\PostUnlocked;

trait LocksPosts
{
    public function lock()
    {
        $this->recordThat(new PostLocked());
        return $this;
    }

    public function unlock()
    {
        $this->recordThat(new PostUnlocked());
        return $this;
    }
}
