<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'table_id' => $this->table_id,
            'order_state' => $this->order_state,
            'order_id' => $this->order_id,
            'estimated_time' => $this->estimated_time,
            'total' => $this->total,
            'relationship' => [
                'table' => $this->table,
                'order_items' => OrderItemResource::collection($this->orderItems),
            ]
        ];
    }
}
