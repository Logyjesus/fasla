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
            'order_item' => new ProductResource($this->product),
            'quantity' => $this->quantity,
            'price' => $this->price,
            'color' => $this->color,
            'store_name' => $this->product->seller->store_name,
        ];
    }
}
