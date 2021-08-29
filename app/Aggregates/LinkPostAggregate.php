<?php

namespace App\Aggregates;

use App\StorableEvents\Data\LinkPostCreatedData;
use App\StorableEvents\Data\LinkPostUpdatedData;
use App\StorableEvents\LinkPostCreated;
use App\StorableEvents\LinkPostDeleted;
use App\StorableEvents\LinkPostUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class LinkPostAggregate extends AggregateRoot
{
    use PinsPosts, RestoresPosts;

    public function create(string $authorUuid, string $title, string $groupUuid, string $link): static
    {
        $this->recordThat(new LinkPostCreated(new LinkPostCreatedData($authorUuid, $title, $groupUuid, $link)));
        return $this;
    }

    public function update(string $title): static
    {
        $this->recordThat(new LinkPostUpdated(new LinkPostUpdatedData($title)));
        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new LinkPostDeleted());
        return $this;
    }
}
