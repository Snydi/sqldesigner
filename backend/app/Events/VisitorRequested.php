<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VisitorRequested implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly string $shareToken) {}

    public function broadcastOn(): array
    {
        return [new PresenceChannel('diagram.' . $this->shareToken)];
    }

    public function broadcastAs(): string
    {
        return 'visitor.requested';
    }

    public function broadcastWith(): array
    {
        return [];
    }
}
