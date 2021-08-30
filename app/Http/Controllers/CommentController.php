<?php

namespace App\Http\Controllers;

use App\Aggregates\CommentAggregate;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentController extends Controller
{
    public function store(string $postUuid, StoreCommentRequest $request)
    {
        $post = Post::where('uuid', $postUuid)->firstOrFail();

        if($post->locked_at && $request->user()->cannot('update', $post)){
            abort(403);
        }

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

        return new CommentResource($comment->refresh());
    }

    public function delete(string $commentUuid, Request $request)
    {
        $comment = Comment::where('uuid', $commentUuid)->firstOrFail();

        if ($request->user()->cannot('update', $comment)) {
            abort(403);
        }

        CommentAggregate::retrieve($commentUuid)
            ->delete()
            ->persist();

        return new CommentResource($comment->refresh());
    }

    public function restore(string $commentUuid, Request $request)
    {
        $comment = Comment::withTrashed()->where('uuid', $commentUuid)->firstOrFail();

        if ($request->user()->cannot('restore', $comment)) {
            abort(403);
        }

        CommentAggregate::retrieve($commentUuid)
            ->restore()
            ->persist();

        return new CommentResource($comment->refresh());
    }

    public function lock($uuid, Request $request)
    {
        $comment = Comment::where('uuid', $uuid)->firstOrFail();

        if ($request->user()->cannot('lock', $comment)) {
            abort(403);
        }

        CommentAggregate::retrieve($uuid)
            ->lock()
            ->persist();

        return new CommentResource($comment->refresh());
    }

    public function unlock($uuid, Request $request)
    {
        $comment = Comment::where('uuid', $uuid)->firstOrFail();

        if ($request->user()->cannot('unlock', $comment)) {
            abort(403);
        }

        CommentAggregate::retrieve($uuid)
            ->unlock()
            ->persist();

        return new CommentResource($comment->refresh());
    }
}
