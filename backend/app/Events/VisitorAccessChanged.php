<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VisitorAccessChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $userId,
        public readonly string $shareToken,
        public readonly string $access, // 'read' | 'write' | 'revoked'
    ) {}

    public function broadcastOn(): array
    {
        return [new PresenceChannel('diagram.' . $this->shareToken)];
    }

    public function broadcastAs(): string
    {
        return 'visitor.access.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'access'  => $this->access,
        ];
    }
}
