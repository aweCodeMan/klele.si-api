<?php

namespace App\Console\Commands;

use App\Models\Link;
use Illuminate\Console\Command;

class UpdateMissingLinksMetaDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'klele:update-missing-links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Try to update all of the missing meta data for links';

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
        $links = Link::whereNull('meta')->select('uuid')->cursor();

        foreach ($links as $linkUuid) {
            Link::updateMetaData($linkUuid);
        }

        return 0;
    }
}
