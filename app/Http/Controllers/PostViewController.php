<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreViewRequest;
use App\Models\Post;
use App\Models\PostView;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostViewController extends Controller
{
    public function store(StoreViewRequest $request)
    {
        $post = Post::where('uuid', $request->get('postUuid'))->firstOrFail();

        $view = PostView::updateOrCreate([
            'post_uuid' => $post->uuid,
            'user_uuid' => $request->user()->uuid,
        ], ['number_of_comments' => $post->number_of_comments]);

        return response()->json();
    }
}
