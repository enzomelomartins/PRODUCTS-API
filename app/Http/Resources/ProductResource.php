<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => 'R$ ' . $this->price,
            'status' => $this->status,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'attachments' => $this->when(
                $this->attachments && $this->attachments->isNotEmpty(),
                AttachmentResource::collection($this->attachments)
            ),
            'category' => ['name' => $this->category->name],
        ];
    }
}
