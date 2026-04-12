<?php

namespace App\Events;

use App\Enums\DiagramAccess;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VisitorAccessChanged implements ShouldBroadcastNow //Don't believe the IDE, every method here is used
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $userId,
        public readonly string $shareToken,
        public readonly DiagramAccess $access,
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
            'access'  => $this->access->value,
        ];
    }
}
