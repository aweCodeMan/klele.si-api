<?php

namespace App\Reactors;

use App\Models\Comment;
use App\Models\Post;
use App\Notifications\NewReplyNotification;
use App\StorableEvents\CommentCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;
use function Symfony\Component\String\b;

class NotificationReactor extends Reactor implements ShouldQueue
{
    public function onCommentCreated(CommentCreated $event)
    {
        $notifiable = $this->getNotifiable($event);

        if ($notifiable) {
            $notifiable->notify(new NewReplyNotification($event));
            $this->incrementUnreadNotification($notifiable);
        }
    }

    private function isReplyToAPost(CommentCreated $event): bool
    {
        return $event->data->parent_uuid === null;
    }

    private function incrementUnreadNotification($author)
    {
        $stats = DB::table('user_counters')->where('user_uuid', '=', $author->uuid)->first();

        if ($stats) {
            DB::table('user_counters')->where('user_uuid', '=', $author->uuid)->increment('number_of_unread_notifications');
        } else {
            DB::table('user_counters')->insert(['user_uuid' => $author->uuid, 'number_of_unread_notifications' => 1]);
        }
    }

    private function getNotifiable(CommentCreated $event)
    {
        if ($this->isReplyToAPost($event)) {
            $post = Post::where('uuid', $event->data->root_uuid)->with('author')->first();

            if ($post && $post->author->uuid !== $event->data->author_uuid) {
                return $post->author;
            }
        } else {
            $comment = Comment::where('uuid', $event->data->parent_uuid)->with('author')->first();

            if ($comment && $comment->author->uuid !== $event->data->author_uuid) {
                return $comment->author;
            }
        }

        return null;
    }
}
