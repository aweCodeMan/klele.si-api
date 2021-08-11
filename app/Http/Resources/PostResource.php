<?php

namespace App\Http\Resources;

use App\Models\Post;
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
            'slug' => $this->slug,
            'title' => !$this->deleted_at ? $this->title : Post::TEXT_DELETED,
            'postType' => $this->post_type,
            'content' => $this->getContent(),
            'createdAt' => $this->created_at,
            'deletedAt' => $this->deleted_at,
        ];
    }

    private function getContent()
    {
        if ($this->markdown) {
            return ['html' => !$this->deleted_at ? $this->markdown->html : Post::TEXT_DELETED,];
        }

        return null;
    }
}
