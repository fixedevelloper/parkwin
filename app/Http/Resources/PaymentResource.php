<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{

        public function toArray($request)
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'method' => $this->method_pay,
            'status' => $this->status?'SUCCESS':'FAILED',
            'type' => $this->type,
            'reference' => $this->reference,
            'created_at' => $this->created_at,
            'date' => $this->created_at->format('Y-m-d H:i'),
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],

            'session' => $this->session ? [
                'id' => $this->session->id,
                'cycle_number' => $this->session->cycle_number,
                'amount' => $this->session->amount,
            ] : null,
        ];

    }
}
