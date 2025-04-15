<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FoodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'calories' => $this->calories,
            'proteins' => $this->proteins,
            'glucides' => $this->glucides,
            'lipides' => $this->lipides,
            'category' => $this->category,
            'image_url' => Storage::disk('s3')->url($this->image),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
