<?php

namespace App\Http\Controllers;

use App\Aggregates\CommentAggregate;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(string $postUuid, StoreCommentRequest $request)
    {
        $post = Post::where('uuid', $postUuid)->firstOrFail();

        $uuid = $this->generateUuid();

        CommentAggregate::retrieve($uuid)
            ->create($request->user()->uuid, $postUuid, $request->get('parentUuid'), $request->get('markdown'))
            ->persist();

        return new CommentResource(Comment::where('uuid', $uuid)->first());
    }

    public function update(string $commentUuid, UpdateCommentRequest $request)
    {
        $comment = Comment::where('uuid', $commentUuid)->firstOrFail();

        if ($request->user()->cannot('update', $comment)) {
            abort(403);
        }

        CommentAggregate::retrieve($commentUuid)
            ->update($request->get('markdown'))
            ->persist();

        return response()->json();
    }
}
