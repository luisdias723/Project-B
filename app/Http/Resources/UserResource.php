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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'group_id' => $this->group_id,
            'vac_days'=>$this->vac_days,
            'roles' => array_map(
                function ($role) {
                    return $role['name'];
                },
                $this->roles->toArray()
            ),
            'permissions' => array_map(
                function ($permission) {
                    return $permission['name'];
                },
                $this->getAllPermissions()->toArray()
            ),
            'mobile_code' => $this->mobile_code,
            'avatar' => $this->profile_photo_path,
            'birthday' => $this->birthday,
            'age' => $this->birthday ? floor((time() - strtotime($this->birthday)) / 31556926) : null,
            'active' => $this->active,
            'address' => $this->address,
            'zipcode' => $this->zipcode,
            'city' => $this->city,
            'country' => $this->country,
            'nif' => $this->nif,
            'iban' => $this->iban,
            'created_at' => $this->created_at ? date_format($this->created_at, 'd-m-Y H:i') : null,
            'num_days' =>$this->num_days ?? 0,
        ];

    }
}
