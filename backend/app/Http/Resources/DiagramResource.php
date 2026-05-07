<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\ResponseField;

class DiagramResource extends JsonResource
{
    #[ResponseField("id", "int", "The diagram ID.")]
    #[ResponseField("name", "string", "The diagram name.")]
    #[ResponseField("db_type", "string", "The database type.", enum: ["mysql", "postgresql"])]
    #[ResponseField("schema", "object", "The diagram schema (tables, columns, relations).")]
    #[ResponseField("script", "string", "The raw SQL script.")]
    #[ResponseField("share_token", "string", "Token used to share the diagram publicly.")]
    #[ResponseField("share_access", "string", "The share access level.")]
    #[ResponseField("require_approval", "boolean", "Whether viewer access requires approval.")]
    #[ResponseField("library", "boolean", "Whether the diagram is in the public library.")]
    #[ResponseField("is_owner", "boolean", "Whether the authenticated user owns this diagram.")]
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
            'require_approval' => (bool) $this->require_approval,
            'library' => (bool) $this->library,
            'is_owner' => $request->user()?->id === $this->user_id,
        ];
    }
}