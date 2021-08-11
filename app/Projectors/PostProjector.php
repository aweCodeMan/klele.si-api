<?php

namespace App\Projectors;

use App\Models\Markdown;
use App\Models\Post;
use App\StorableEvents\MarkdownPostCreated;
use App\StorableEvents\MarkdownPostDeleted;
use App\StorableEvents\MarkdownPostUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PostProjector extends Projector
{
    public function onMarkdownPostCreated(MarkdownPostCreated $event)
    {
        Post::create([
            'uuid' => $event->aggregateRootUuid(),
            'author_uuid' => $event->data->author_uuid,
            'group_uuid' => $event->data->group_uuid,
            'title' => $event->data->title,
            'post_type' => Post::TYPE_MARKDOWN,
        ]);

        Markdown::create([
            'uuid' => $event->aggregateRootUuid(),
            'markdown' => $event->data->markdown,
            'html' => $this->parseMarkdown($event->data->markdown),
        ]);
    }

    public function onMarkdownPostUpdate(MarkdownPostUpdated $event)
    {
        Post::where('uuid', $event->aggregateRootUuid())->update([
            'title' => $event->data->title,
        ]);

        Markdown::where('uuid', $event->aggregateRootUuid())->update([
            'markdown' => $event->data->markdown,
            'html' => (new \Parsedown())->setSafeMode(true)->text($event->data->markdown),
        ]);
    }

    public function onMarkdownPostDelete(MarkdownPostDeleted $event)
    {
        Post::where('uuid', $event->aggregateRootUuid())->delete();
    }

    private function parseMarkdown(string $markdown): string
    {
        $html = (new \Parsedown())->setSafeMode(true)->text($markdown);
        $purifier = new \HTMLPurifier(null);

        return $purifier->purify($html);
    }
}
