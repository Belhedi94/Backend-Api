<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'username' => $this->username,
            'birthday' => $this->birthday,
            'sex' => $this->sex,
            'mobile_number' => $this->mobile_number,
            'avatar' => $this->avatar,
            'country' => $this->country->name,
            'role' => $this->role->name,
            'is_admin' => $this->is_admin,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'posts' => $this->posts
        ];
    }
}
