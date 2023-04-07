<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        //create teacher resource

        return [
            'id' => $this->id,
            'teacher_id' => $this->user->username,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->getFullNameAttribute(),
            'other_name' => $this->other_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'date_of_birth' => $this->date_of_birth,
            'enrollment_status' => $this->enrollment_status,
            'parent_first_name'=> $this->parent_first_name,
            'parent_last_name'=> $this->parent_last_name,
            'parent_phone_number_1'=> $this->parent_phone_number_1,
            'parent_phone_number_2'=> $this->parent_phone_number_2,
            'parent_home_address' => $this->parent_home_address,
            'parent_emergency_contact' => $this->parent_emergency_contact,
            'user_id' => $this->user_id,
            'class' => $this->classLevel->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
