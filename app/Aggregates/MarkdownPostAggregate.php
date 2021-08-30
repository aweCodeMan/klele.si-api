<?php

namespace App\Aggregates;

use App\StorableEvents\Data\MarkdownPostCreatedData;
use App\StorableEvents\Data\MarkdownPostUpdatedData;
use App\StorableEvents\MarkdownPostCreated;
use App\StorableEvents\MarkdownPostDeleted;
use App\StorableEvents\MarkdownPostUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class MarkdownPostAggregate extends AggregateRoot
{
    use PinsPosts, RestoresPosts, LocksPosts;

    public function create(string $authorUuid, string $title, string $groupUuid, string $markdown): static
    {
        $this->recordThat(new MarkdownPostCreated(new MarkdownPostCreatedData($authorUuid, $title, $groupUuid, $markdown)));
        return $this;
    }

    public function update(string $title, string $markdown): static
    {
        $this->recordThat(new MarkdownPostUpdated(new MarkdownPostUpdatedData($title, $markdown)));
        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new MarkdownPostDeleted());
        return $this;
    }
}
