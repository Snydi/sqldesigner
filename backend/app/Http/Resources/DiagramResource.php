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
            'share_token' => $this->share_token,
            'share_access' => $this->share_access,
            'is_owner' => $request->user()?->id === $this->user_id,
        ];
    }
}