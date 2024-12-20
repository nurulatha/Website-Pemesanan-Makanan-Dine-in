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
        return
            [
                'id' => $this->id,
                'table_id' => $this->table_id,
                'customer_name' => $this->customer_name,
                'customer_phone' => $this->customer_phone,
                'orderItems' => OrderItemResource::collection($this->whenLoaded('orderItems'))
            ];
    }
}
