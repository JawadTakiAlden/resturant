<?php

namespace App\Http\Resources;

use App\Status\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PasOrderResource extends JsonResource
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
            'in_progress' => $this->in_progress,
            'total' => $this->total,
            'relationships' => [
                'table' => $this->table,
                'ready_sub_orders' => OrderResource::collection($this->subOrders->filter(function ($subOrder) {
                    return $subOrder->order_state === OrderStatus::Ready;
                }))
            ]
        ];
    }
}
