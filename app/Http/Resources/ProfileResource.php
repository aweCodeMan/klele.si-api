<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return[
            'uuid' => $this->uuid,
            'name' => $this->name,
            'surname' => $this->surname,
            'fullName' => $this->full_name,
            'nickname' => $this->nickname,
            'email' => $this->email,
            'verifiedAt' => $this->email_verified_at,
        ];
    }
}
