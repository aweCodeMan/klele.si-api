<?php

namespace App\Http\Resources;

use App\Models\Comment;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'groupUuid' => $this->group_uuid,
            'group' => new GroupResource($this->group),
            'author' => $this->whenLoaded('author', function () {
                return new AuthorResource($this->author);
            }),
            'slug' => $this->slug,
            'title' => !$this->deleted_at ? $this->title : Post::TEXT_DELETED,
            'postType' => $this->post_type,
            'content' => $this->getContent(),
            'postView' => $this->when($request->user(), function () {
                return new PostViewResource($this->postView);
            }),
            'voted' => $this->when($request->user(), function () {
                if ($this->voted) {
                    return $this->voted->vote;
                }

                return null;
            }),
            'comments' => $this->whenLoaded('comments', function () {
                $groups = $this->comments->groupBy('parent_uuid');

                $postComments = $groups[""] ?? [];
                unset($groups[""]);

                $postComments = $this->makeCommentTree($postComments, $groups);

                return CommentResource::collection($postComments);
            }),
            'numberOfComments' => $this->number_of_comments,
            'score' => new ScoreResource($this->score),
            'pinnedAt' => $this->pinned_at,
            'pinnedUntil' => $this->pinned_until,
            'pinnedProgress' => !$this->pinned_until ? 0 : $this->calculatePinnedProgress($this->pinned_at, $this->pinned_until),
            'pinnedDaysToGo' => !$this->pinned_until ? null : (now()->diffInDays($this->pinned_until) + 1),
            'lockedAt' => $this->locked_at,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'deletedAt' => $this->deleted_at,
        ];
    }

    private function getContent()
    {
        if ($this->markdown) {
            return ['html' => !$this->deleted_at ? $this->markdown->html : Post::TEXT_DELETED,];
        } else if ($this->link) {
            return [
                'link' => !$this->deleted_at ? $this->link->link : null,
                'data' => !$this->deleted_at ? $this->link->meta : null,
                'domain' => !$this->deleted_at ? parse_url($this->link->link)['host'] : null,
            ];
        }

        return null;
    }

    private function makeCommentTree(iterable $postComments, iterable $groups): iterable
    {
        foreach ($postComments as $postComment) {
            if (isset($groups[$postComment->uuid])) {
                $ungroup = $groups[$postComment->uuid];
                unset($groups[$postComment->uuid]);

                $postComment->comments = CommentResource::collection($this->makeCommentTree($ungroup, $groups));

                if (count($groups) === 0) {
                    break;
                }
            }
        }

        return $postComments;
    }

    private function calculatePinnedProgress(Carbon $pinnedAt, Carbon $pinnedUntil)
    {
        $fullRangeInDays = $pinnedAt->diffInDays($pinnedUntil) + 1;
        $currentRangeInDays = $pinnedAt->diffInDays(now()) + 1;

        return (int) round(($currentRangeInDays / $fullRangeInDays) * 100);
    }
}
