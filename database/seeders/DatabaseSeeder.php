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
            'color' => '#434343',
            'order' => 99,
        ]);

        Group::create([
            'uuid' => Str::orderedUuid(),
            'name' => 'oblikovanje',
            'slug' => 'oblikovanje',
            'color' => '#FF9314',
            'order' => 2,
        ]);

        Group::create([
            'uuid' => Str::orderedUuid(),
            'name' => 'ux',
            'slug' => 'ux',
            'color' => '#C80000',
            'order' => 3,
        ]);

        Group::create([
            'uuid' => Str::orderedUuid(),
            'name' => 'tehnologija',
            'slug' => 'tehnologija',
            'color' => '#10C800',
            'order' => 4,
        ]);

        Group::create([
            'uuid' => Str::orderedUuid(),
            'name' => 'na čem delaš?',
            'slug' => 'na-cem-delas',
            'color' => '#C400C8',
            'order' => 5,
        ]);

    }
}
