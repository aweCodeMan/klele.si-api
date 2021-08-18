<?php

namespace App\Console\Commands;

use App\Aggregates\MarkdownPostAggregate;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateWeeklyPinnedPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'klele:create-weekly-pinned-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates all of the weekly pinned posts.';

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
        $groupUuid = Group::where('slug', 'programiranje')->first()->uuid;

        $currentDayOfWeek = now()->dayOfWeek;
        $lastMonday = now()->isMonday() ? now() : now()->subDays($currentDayOfWeek === 0 ? 6 : ($currentDayOfWeek - 1));

        MarkdownPostAggregate::retrieve(Str::orderedUuid()->toString())
            ->create(User::first()->uuid, 'Tedenska objava za butasta vprašanja', $groupUuid, "Imaš vprašanje za katerega ne želiš odpirati novih prispevkov?\n\nSuper! Vprašaj tu!")
            ->pin($lastMonday->toDateString(), $lastMonday->addDays(6)->toDateString())
            ->persist();


        return 0;
    }
}
