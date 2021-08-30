<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('group_uuid')->index();
            $table->uuid('author_uuid')->index();
            $table->string('slug')->unique();
            $table->string('title');
            $table->unsignedTinyInteger('post_type');

            $table->unsignedSmallInteger('number_of_comments')->default(0);

            $table->date('pinned_at')->nullable()->index();
            $table->date('pinned_until')->nullable()->index();
            $table->date('locked_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
