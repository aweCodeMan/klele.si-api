<?php

namespace App\Http\Controllers;

use App\Aggregates\LinkPostAggregate;
use App\Aggregates\MarkdownPostAggregate;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostFormResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostController extends Controller
{
    public function store(CreatePostRequest $request)
    {
        switch ($request->get('postType')) {
            case Post::TYPE_MARKDOWN:
                $uuid = $this->createMarkdownPost($request);
                break;
            case Post::TYPE_LINK:
                $uuid = $this->createLinkPost($request);
                break;
        }

        $post = Post::withTrashed()->with('markdown', 'author', 'comments', 'score', 'comments.score')->where('uuid', $uuid)->firstOrFail();

        return new PostResource($post);
    }

    public function update($uuid, UpdatePostRequest $request)
    {
        $post = Post::where('uuid', $uuid)->firstOrFail();

        if ($request->user()->cannot('update', $post)) {
            abort(403);
        }

        $rules = ['title' => ['required']];

        if ($post->post_type == Post::TYPE_MARKDOWN) {
            $rules['markdown'] = ['required'];
        }

        $this->validate($request, $rules);

        switch ($post->post_type) {
            case Post::TYPE_MARKDOWN:
                $this->updateMarkdownPost($post->uuid, $request);
                break;
            case Post::TYPE_LINK:
                $this->updateLinkPost($post->uuid, $request);
                break;
        }

        return new PostResource($post->refresh());
    }

    public function delete($uuid, Request $request)
    {
        $post = Post::where('uuid', $uuid)->firstOrFail();

        if ($request->user()->cannot('delete', $post)) {
            abort(403);
        }

        switch ($post->post_type) {
            case Post::TYPE_MARKDOWN:
                $this->deleteMarkdownPost($uuid);
                break;
            case Post::TYPE_LINK:
                $this->deleteLinkPost($post->uuid);
                break;
        }
    }

    public function form($uuid, Request $request)
    {
        $post = Post::with('markdown')->where('slug', $uuid)->orWhere('uuid', $uuid)->firstOrFail();

        if ($request->user()->cannot('update', $post)) {
            abort(403);
        }

        return new PostFormResource($post);
    }

    public function show($slug, Request $request)
    {
        $post = Post::withTrashed()->with('markdown', 'author', 'comments', 'score', 'comments.score')->where('slug', $slug)->orWhere('uuid', $slug)->firstOrFail();

        return new PostResource($post);
    }

    private function createMarkdownPost(CreatePostRequest $request): string
    {
        $uuid = $this->generateUuid();

        MarkdownPostAggregate::retrieve($uuid)
            ->create($request->user()->uuid, $request->get('title'), $request->get('groupUuid'), $request->get('markdown'))
            ->persist();

        return $uuid;
    }

    private function updateMarkdownPost($uuid, UpdatePostRequest $request)
    {
        MarkdownPostAggregate::retrieve($uuid)
            ->update($request->get('title'), $request->get('markdown'))
            ->persist();

        return $uuid;
    }

    private function deleteMarkdownPost($uuid)
    {
        MarkdownPostAggregate::retrieve($uuid)
            ->delete()
            ->persist();

        return $uuid;
    }

    private function createLinkPost(CreatePostRequest $request)
    {
        $uuid = $this->generateUuid();

        LinkPostAggregate::retrieve($uuid)
            ->create($request->user()->uuid, $request->get('title'), $request->get('groupUuid'), $request->get('link'))
            ->persist();

        return $uuid;
    }

    private function updateLinkPost($uuid, UpdatePostRequest $request)
    {
        LinkPostAggregate::retrieve($uuid)
            ->update($request->get('title'))
            ->persist();

        return $uuid;
    }

    private function deleteLinkPost($uuid)
    {
        LinkPostAggregate::retrieve($uuid)
            ->delete()
            ->persist();

        return $uuid;
    }
}
