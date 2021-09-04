<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->id,
            'notificationType' => $this->type,
            'data' => [
                'author' => $this->data['author'],
                'parentType' => $this->data['parent_type'],
                'commentUuid' => $this->data['comment_uuid'],
                'postSlug' => $this->data['post_slug'],
            ],
            'readAt' => $this->read_at,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
