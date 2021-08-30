<?php

namespace App\Projectors;

use App\Models\Comment;
use App\Models\Markdown;
use App\Services\MarkdownService;
use App\StorableEvents\CommentCreated;
use App\StorableEvents\CommentDeleted;
use App\StorableEvents\CommentLocked;
use App\StorableEvents\CommentRestored;
use App\StorableEvents\CommentUnlocked;
use App\StorableEvents\CommentUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CommentProjector extends Projector
{
    public function onCommentCreated(CommentCreated $event)
    {
        Comment::create([
            'uuid' => $event->aggregateRootUuid(),
            'parent_uuid' => $event->data->parent_uuid,
            'root_uuid' => $event->data->root_uuid,
            'author_uuid' => $event->data->author_uuid,
        ]);

        Markdown::create([
            'uuid' => $event->aggregateRootUuid(),
            'markdown' => $event->data->markdown,
            'html' => MarkdownService::parse($event->data->markdown),
        ]);
    }

    public function onCommentUpdated(CommentUpdated $event)
    {
        Comment::where('uuid', $event->aggregateRootUuid())->first()->touch();

        Markdown::where('uuid', $event->aggregateRootUuid())->update([
            'markdown' => $event->data->markdown,
            'html' => MarkdownService::parse($event->data->markdown),
        ]);
    }

    public function onCommentDeleted(CommentDeleted $event)
    {
        Comment::where('uuid', $event->aggregateRootUuid())->delete();
    }

    public function onCommentRestored(CommentRestored $event)
    {
        Comment::withTrashed()->where('uuid', $event->aggregateRootUuid())->restore();
    }

    public function onCommentLocked(CommentLocked $event)
    {
        Comment::withTrashed()->where('uuid', $event->aggregateRootUuid())->update(['locked_at' => $event->createdAt()]);
    }

    public function onCommentUnlocked(CommentUnlocked $event)
    {
        Comment::withTrashed()->where('uuid', $event->aggregateRootUuid())->update(['locked_at' => null]);
    }
}
