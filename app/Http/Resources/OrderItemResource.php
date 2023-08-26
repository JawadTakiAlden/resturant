<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sub_order_id' => $this->sub_order_id,
            'quantity' => $this->quantity,
            'meal_id' => $this->meal_id,
            'note' => $this->note,
            'added' => $this->added,
            'removed' => $this->removed,
            'total' => $this->total,
            'relationships' => [
                'meal' => $this->meal
            ]
        ];
    }
}
