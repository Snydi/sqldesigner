<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DiagramResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'db_type' => $this->db_type ?? 'mysql',
            'schema' => $this->schema,
            'script' => $this->script,
        ];
    }
}