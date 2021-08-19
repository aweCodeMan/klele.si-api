<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostParsingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_parsings', function (Blueprint $table) {
            $table->id();
            $table->string('feed_url')->index();
            $table->string('feed_item_id')->index();
            $table->text('feed_item_link');
            $table->uuid('post_uuid')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_parsings');
    }
}
