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
            'birthdate' => $this->birthdate,
            'sexe' => $this->sexe,
            'mobile_number' => $this->mobile_number,
            'avatar' => $this->avatar,
            'is_admin' => $this->is_admin,
            'is_banned' => $this->is_banned,
            'role' => $this->role->name,
            'country' => $this->country->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
