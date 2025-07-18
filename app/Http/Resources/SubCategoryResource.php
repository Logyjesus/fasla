<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
{
    return [
        'id' => $this->id, // ✅ أضف هذا السطر
        'name' => $this->name,
        'slug' => $this->slug,
        'image' => asset('images/' . $this->image),
        'category' => new CategoryResource($this->category),
    ];
}

}