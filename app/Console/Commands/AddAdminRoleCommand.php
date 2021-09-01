<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AddAdminRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'klele:add-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add admin role to user.';

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
        $email = null;

        while (!$email) {
            $email = $this->ask('User email');

            $user = User::where('email', $email)->first();

            if (!$user) {
                $this->error("Email $email does not exist.");
                continue;
            }

            $user->assignRole('admin');
            $this->line("Added admin to $email");
        }

        return 0;
    }
}
