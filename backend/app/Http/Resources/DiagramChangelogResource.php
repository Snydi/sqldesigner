<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\ResponseField;

class DiagramChangelogResource extends JsonResource
{
    #[ResponseField("id", "int", "The changelog entry ID.")]
    #[ResponseField("diagram_id", "int", "The associated diagram ID.")]
    #[ResponseField("user_id", "int", "The ID of the user who made the change.")]
    #[ResponseField("user_name", "string", "The email of the user who made the change.")]
    #[ResponseField("action", "string", "The action performed (e.g. table_added, column_removed).")]
    #[ResponseField("details", "object", "Additional details about the action.", required: false)]
    #[ResponseField("created_at", "string", "ISO 8601 timestamp of when the change was recorded.")]
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'diagram_id' => $this->diagram_id,
            'user_id'    => $this->user_id,
            'user_name'  => $this->user_name,
            'action'     => $this->action,
            'details'    => $this->details,
            'created_at' => $this->created_at,
        ];
    }
}
