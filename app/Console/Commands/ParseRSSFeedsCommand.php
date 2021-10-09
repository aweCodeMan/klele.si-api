<?php

namespace App\Console\Commands;

use App\Aggregates\LinkPostAggregate;
use App\Aggregates\UserAggregate;
use App\Models\Group;
use App\Models\PostParsing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Vedmant\FeedReader\Facades\FeedReader;

class ParseRSSFeedsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'klele:parse-rss-feeds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches and stores new links from set RSS feeds';

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
        $rssAuthor = $this->prepareAuthor();
        $groups = Group::get()->mapWithKeys(function ($item) {
            return [$item->slug => $item];
        });

        $feeds = [
            [
                'rss' => 'https://swizec.com/rss.xml',
                'groupSlug' => 'programiranje',
                'author' => $rssAuthor,
            ],
            [
                'rss' => 'https://martinfowler.com/feed.atom',
                'groupSlug' => 'programiranje',
                'author' => $rssAuthor,
            ],
            [
                'rss' => 'https://www.reddit.com/r/programming.json',
                'groupSlug' => 'programiranje',
                'author' => $rssAuthor,
                'type' => 'reddit'
            ],
            [
                'rss' => 'https://mariokranjec.dev/rss.xml',
                'groupSlug' => 'programiranje',
                'author' => $rssAuthor,
            ],
            [
                'rss' => 'https://thedailycoach.substack.com/feed',
                'groupSlug' => 'razno',
                'author' => $rssAuthor,
            ]
        ];

        $timeLimit = now()->subMonth();

        foreach ($feeds as $feed) {
            if (isset($feed['type']) && $feed['type'] === 'reddit') {
                $reader = json_decode(file_get_contents($feed['rss']), true);

                $items = collect($reader['data']['children'])->filter(function ($item) {
                    $item = $item['data'];
                    return isset($item['is_self']) && !$item['is_self'] && $item['ups'] >= 50;
                })->map(function ($item) {
                    $item = $item['data'];
                    return new Item($item['name'], $item['title'], $item['url_overridden_by_dest'], $item['created']);
                })->toArray();

                $reader = new Feed($items);
            } else {
                $reader = FeedReader::read($feed['rss']);
            }

            foreach ($reader->get_items() as $feedItem) {
                //  Check if the link is too old
                $feedTime = Carbon::parse($feedItem->get_date());

                if ($feedTime->isBefore($timeLimit)) {
                    break;
                }

                //  Check if we already parsed this link
                $postParsing = PostParsing::where('feed_url', $feed['rss'])
                    ->where('feed_item_id', $feedItem->get_id())
                    ->first();

                if (!$postParsing) {
                    $uuid = Str::orderedUuid()->toString();

                    LinkPostAggregate::retrieve($uuid)
                        ->create($feed['author']->uuid, $feedItem->get_title(), $groups[$feed['groupSlug']]->uuid, $feedItem->get_link())
                        ->persist();

                    PostParsing::create(['feed_url' => $feed['rss'], 'feed_item_id' => $feedItem->get_id(), 'post_uuid' => $uuid, 'feed_item_link' => $feedItem->get_link()]);
                }
            }
        }

        return 0;
    }

    private function prepareAuthor()
    {
        $email = 'info@klele.si';

        $user = User::where('email', $email)->first();

        if ($user) {
            return $user;
        }

        $uuid = Str::orderedUuid()->toString();
        UserAggregate::retrieve($uuid)
            ->register('🕫', '', 'klele.si', $email)
            ->persist();

        $user = User::where('uuid', $uuid)->first();
        $user->email_verified_at = now();
        $user->save();

        return $user;
    }
}

class Feed
{
    public function __construct(private $items)
    {
    }

    function get_items()
    {
        return $this->items;
    }
}

class Item
{
    public function __construct(private $id, private $title, private $link, private $date)
    {
    }

    function get_date()
    {
        return $this->date;
    }

    function get_id()
    {
        return $this->id;
    }

    function get_title()
    {
        return $this->title;
    }

    function get_link()
    {
        return $this->link;
    }
}
