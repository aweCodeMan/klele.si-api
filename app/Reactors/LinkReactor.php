<?php

namespace App\Reactors;

use App\Models\Link;
use App\Services\LinkService;
use App\StorableEvents\LinkPostCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class LinkReactor extends Reactor implements ShouldQueue
{
    public function onLinkPostCreated(LinkPostCreated $event)
    {
        Link::updateMetaData($event->aggregateRootUuid());
    }
}
