<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class NewActivityNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(private array $data)
    {
        //
    }

    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->from('klele.si', ':tada:')
            ->to('#klele')
            ->content("*Klele se dogaja!* (zadnjih 30 minut)\n")
            ->attachment(function ($attachment) {
                $content = "";

                if ($this->data['users']) {
                    $content .= sprintf("uporabniki: *%s*\n", $this->data['users']);
                }

                if ($this->data['posts']) {
                    $content .= sprintf("prispevki: *%s*\n", $this->data['posts']);
                }

                if ($this->data['system_posts']) {
                    $content .= sprintf("sistemski prispevki: *%s*\n", $this->data['system_posts']);
                }

                if ($this->data['comments']) {
                    $content .= sprintf("komentarji: *%s*\n", $this->data['comments']);
                }

                if ($this->data['votes']) {
                    $content .= sprintf("srčki: *%s*\n", $this->data['votes']);
                }

                $attachment->markdown(['text'])
                    ->content($content);
            });
    }
}
