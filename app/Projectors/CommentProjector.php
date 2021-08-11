<?php

namespace App\Projectors;

use App\Models\Comment;
use App\Models\Markdown;
use App\Services\MarkdownService;
use App\StorableEvents\CommentCreated;
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
}
