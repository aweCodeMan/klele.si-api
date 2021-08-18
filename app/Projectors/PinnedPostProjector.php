<?php

namespace App\Projectors;

use App\Models\Post;
use App\StorableEvents\PostPinned;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PinnedPostProjector extends Projector
{
    public function onPinnedPost(PostPinned $event)
    {
        Post::where('uuid', $event->aggregateRootUuid())
            ->update(['pinned_at' => $event->data->pinned_at, 'pinned_until' => $event->data->pinned_until]);
    }
}
