<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PinnedPostController extends Controller
{
    public function index(FeedRequest $request)
    {
        $posts = Post::with(['link', 'markdown', 'author', 'group'])
            ->where(function ($query) use ($request) {
                if ($request->has('groupUuid')) {
                    $query->where('group_uuid', $request->get('groupUuid'));
                }
            })
            ->whereNotNull('pinned_at')
            ->orderByDesc('created_at')
            ->paginate($request->has('perPage') ? $request->get('perPage') : 50);

        return PostResource::collection($posts);
    }
}
