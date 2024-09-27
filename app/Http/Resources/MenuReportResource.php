<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'menu_id' => $this->id,
            'menu_name' => $this->name,
            'category_name' => $this->category->name,
            'menu_price' => $this->price,
            'total_quantity' => $this->total_quantity,
            'total_sales' => $this->total_sales,
        ];
    }
}
