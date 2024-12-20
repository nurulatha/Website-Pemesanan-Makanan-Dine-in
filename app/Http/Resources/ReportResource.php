<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order_item_id' => $this->id,
            'customer_name' => $this->order->customer_name,
            'customer_phone' => $this->order->customer_phone,
            'table_id' => $this->order->table_id,
            'menu' => $this->menu->name,
            'category' => $this->menu->category->name,
            'price' => $this->menu->price,
            'quantity' => $this->quantity,
            'total_price' => $this->total_price
        ];
    }
}
