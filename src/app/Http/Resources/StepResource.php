<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StepResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->step_title,
            'content' => $this->content,
            'image_url' => $this->image_url,
            'order' => $this->order,
        ];
    }
}
