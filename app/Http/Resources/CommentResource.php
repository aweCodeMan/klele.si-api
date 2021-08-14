<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'rootUuid' => $this->root_uuid,
            'parentUuid' => $this->parent_uuid,
            'author' => new AuthorResource($this->author),
            'html' => !$this->deleted_at ? $this->markdown->html : Comment::TEXT_DELETED,
            'comments' => CommentResource::collection($this->comments),
            'score' => new ScoreResource($this->score),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'deletedAt' => $this->deleted_at,
        ];
    }
}
