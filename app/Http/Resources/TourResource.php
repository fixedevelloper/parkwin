<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'cycle_number' => $this->cycle_number,
            'amount' => $this->amount,
            'status' => $this->status,

            'beneficiary' => $this->beneficiary ? [
                'id' => $this->beneficiary->id,
                'name' => $this->beneficiary->name,
            ] : null,

            /** 🔑 CLÉ IMPORTANTE **/
            'has_paid_by_me' => $this->payments->isNotEmpty(),
        ];
    }
}

