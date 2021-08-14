<?php

namespace App\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;

class PostFormResource extends JsonResource
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
            'title' => !$this->deleted_at ? $this->title : Post::TEXT_DELETED,
            'postType' => $this->post_type,
            'markdown' => $this->when($this->markdown, function () {
                return $this->markdown->markdown;
            }),
            'link' => $this->when($this->link, function () {
                return $this->link->link;
            }),
        ];
    }
}
