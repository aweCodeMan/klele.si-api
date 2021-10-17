<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Vote;
use App\Notifications\NewActivityNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class NotifyNewActivityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'klele:notify-new-activity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifies the slack channel about new activities.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $systemUser = User::where('email', 'info@klele.si')->first();
        $time = now()->subMinutes(30);

        //  Check users
        $users = User::where('created_at', '>=', $time)->count();

        //  Check posts
        // $systemPosts = Post::where('created_at', '>=', $time)->where('author_uuid', $systemUser->uuid)->count();
        $posts = Post::where('created_at', '>=', $time)->where('author_uuid', '!=', $systemUser->uuid)->count();

        //  Check comments
        $comments = Comment::where('created_at', '>=', $time)->count();

        //  Check votes
        $votes = Vote::where('created_at', '>=', $time)->count();

        $data = [
            'users' => $users,
            'posts' => $posts,
            //'system_posts' => $systemPosts,
            'comments' => $comments,
            'votes' => $votes,
        ];

        $count = array_reduce($data, function ($carry, $item) {
            return $carry + $item;
        }, 0);

        if ($count) {
            Notification::route('slack', env('SLACK_WEBHOOK_URL'))
                ->notify(new NewActivityNotification($data));
        }

        return 0;
    }
}
