<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'category' => $this->category,
            'name' => $this->name,
            'name_ar' => $this->name_ar,
            'visibility' => $this->visibility,
            'position' => $this->position,
            'description' => $this->description,
            'description_ar' => $this->description_ar,
            'category_id' => $this->category_id,
            'Size' => $this->size
        ];
    }
}
