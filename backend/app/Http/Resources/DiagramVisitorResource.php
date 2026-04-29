<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\ResponseField;

class DiagramVisitorResource extends JsonResource
{
    #[ResponseField("id", "int", "The visitor record ID.")]
    #[ResponseField("user_id", "int", "The visitor's user ID.")]
    #[ResponseField("name", "string", "The visitor's display name, falling back to email.")]
    #[ResponseField("email", "string", "The visitor's email address.")]
    #[ResponseField("status", "string", "Approval status.", enum: ["pending", "approved"])]
    #[ResponseField("access", "string", "Access level granted to this visitor.", enum: ["read", "write", "revoke"])]
    public function toArray($request): array
    {
        return [
            'id'      => $this->id,
            'user_id' => $this->user_id,
            'name'    => $this->user->name ?: $this->user->email,
            'email'   => $this->user->email,
            'status'  => $this->status,
            'access'  => $this->access,
        ];
    }
}
