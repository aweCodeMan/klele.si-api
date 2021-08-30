<?php

namespace App\Aggregates;

use App\StorableEvents\CommentCreated;
use App\StorableEvents\CommentDeleted;
use App\StorableEvents\CommentLocked;
use App\StorableEvents\CommentRestored;
use App\StorableEvents\CommentUnlocked;
use App\StorableEvents\CommentUpdated;
use App\StorableEvents\Data\CommentCreatedData;
use App\StorableEvents\Data\CommentUpdatedData;
use App\StorableEvents\Data\LinkPostCreatedData;
use App\StorableEvents\Data\MarkdownPostUpdatedData;
use App\StorableEvents\LinkPostCreated;
use App\StorableEvents\MarkdownPostUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class CommentAggregate extends AggregateRoot
{
    public function create(string $authorUuid, string $rootUuid, ?string $parentUuid, string $markdown): static
    {
        $this->recordThat(new CommentCreated(new CommentCreatedData($authorUuid, $rootUuid, $parentUuid, $markdown)));
        return $this;
    }

    public function update(string $markdown): static
    {
        $this->recordThat(new CommentUpdated(new CommentUpdatedData($markdown)));
        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new CommentDeleted());
        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new CommentRestored());
        return $this;
    }

    public function unlock()
    {
        $this->recordThat(new CommentUnlocked());
        return $this;
    }

    public function lock()
    {
        $this->recordThat(new CommentLocked());
        return $this;
    }
}
