<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            '_id' => (string) $this->getKey(),
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,
            'due_date' => $this->due_date?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

