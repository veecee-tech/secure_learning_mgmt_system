<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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

        //create student resource

        return [
            'id' => $this->id,
            'student_id' => $this->user->username,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->getFullNameAttribute(),
            'other_name' => $this->other_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'date_of_birth' => $this->date_of_birth,
            'enrollment_status' => $this->enrollment_status,
            'user_id' => $this->user_id,
            'class' => $this->classLevel ? $this->classLevel : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
