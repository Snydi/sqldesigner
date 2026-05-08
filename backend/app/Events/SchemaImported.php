<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SchemaImported implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $shareToken,
        public readonly string $schema,
        public readonly string $importedBy,
    ) {}

    public function broadcastOn(): array
    {
        return [new PresenceChannel('diagram.' . $this->shareToken)];
    }

    public function broadcastAs(): string
    {
        return 'schema.imported';
    }

    public function broadcastWith(): array
    {
        return [
            'imported_by' => $this->importedBy,
        ];
    }
}
