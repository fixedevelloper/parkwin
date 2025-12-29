<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'balance' => $this->balance,
            'amount_goal' => $this->amount_goal,
            'progress' => $this->amount_goal > 0
                ? round(($this->balance / $this->amount_goal) * 100, 1)
                : 0,
            'active' => $this->active?true:false,
            'created_at' => $this->created_at->format('d/m/Y')
        ];
    }
}
