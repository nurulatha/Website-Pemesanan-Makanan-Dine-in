<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'menu_id' => $this->menu_id,
            'category' => $this->whenLoaded('menu', $this->menu->category->name),
            'name' => $this->whenLoaded('menu', $this->menu->name),
            'description' => $this->whenLoaded('menu', $this->menu->description),
            'price' => $this->whenLoaded('menu', $this->menu->price),
            'image' => $this->whenLoaded('menu', $this->menu->image),
            'quantity' => $this->quantity,
            'total_price' => $this->total_price,
        ];
    }
}
