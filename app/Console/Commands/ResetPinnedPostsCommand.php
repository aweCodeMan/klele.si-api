<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class ResetPinnedPostsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'klele:reset-pinned-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks and resets and posts that should not be pinned anymore.';

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
        Post::where('pinned_until', '<', now())->update(['pinned_at' => null, 'pinned_until' => null]);

        return 0;
    }
}
