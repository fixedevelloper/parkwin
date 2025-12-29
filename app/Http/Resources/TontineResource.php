<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TontineResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            'frequency' => strtoupper($this->frequency) ,
            'participants_count' => $this->participants_count,
            'owner' => [
                'id' => $this->owner->id,
                'name' => $this->owner->name,
                'phone' => $this->owner->phone
            ],
            'members' => $this->members->map(function($member){
                return [
                    'id' => $member->user->id,
                    'name' => $member->user->name,
                    'phone' => $member->user->phone,
                    'position' => $member->position,
                ];
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'status' => $this->status ?? 'pending', // si tu veux un status,
            'is_owner'=> auth()->id()==$this->owner->id,
            'reference'=>$this->reference
        ];
    }
}

