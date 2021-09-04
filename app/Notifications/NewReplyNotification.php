<?php

namespace App\Notifications;

use App\Http\Resources\AuthorResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\StorableEvents\CommentCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReplyNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public CommentCreated $event, public Post $post)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $author = User::where('uuid', $this->event->data->author_uuid)->first();

        return [
            'comment_uuid' => $this->event->aggregateRootUuid(),
            'author' => (new AuthorResource($author))->toArray(null),
            'parent_type' => $this->event->data->parent_uuid ? Comment::class : Post::class,
            'post_slug' => $this->post->slug,
        ];
    }
}
