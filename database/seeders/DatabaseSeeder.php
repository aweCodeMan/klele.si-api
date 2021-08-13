<?php

namespace Database\Seeders;

use App\Aggregates\UserAggregate;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        Group::create([
            'uuid' => Str::orderedUuid(),
            'name' => 'programiranje',
            'slug' => 'programiranje',
            'color' => '#63c7ff',
            'order' => 0,
        ]);

        Group::create([
            'uuid' => Str::orderedUuid(),
            'name' => 'razno',
            'slug' => 'razno',
            'color' => '#FF9314',
            'order' => 1,
        ]);
    }
}
