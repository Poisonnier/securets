<?php

namespace App\Events;

use App\Models\Integration;
use App\Models\LoginEvent;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired immediately (ShouldBroadcastNow – bypasses the queue) whenever a new
 * login attempt is recorded for an integration. The mobile app listens on the
 * private-user.{id} channel via pusher-js / Laravel Reverb WebSocket.
 */
class LoginAttemptBroadcast implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly LoginEvent $loginEvent,
        public readonly Integration $integration,
    ) {}

    /**
     * The private channel scoped to the owning user so only their device
     * receives the event.
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('user.' . $this->loginEvent->user_id);
    }

    /**
     * Custom event name the mobile Pusher client will listen for.
     */
    public function broadcastAs(): string
    {
        return 'LoginAttemptBroadcast';
    }

    /**
     * Payload delivered to the mobile client.
     */
    public function broadcastWith(): array
    {
        return [
            'id'               => $this->loginEvent->id,
            'event_id'         => $this->loginEvent->id,
            'status'           => $this->loginEvent->status,
            'ip_address'       => $this->loginEvent->ip_address,
            'device'           => $this->loginEvent->device,
            'created_at'       => $this->loginEvent->created_at->toISOString(),
            'integration'      => [
                'id'          => $this->integration->id,
                'name'        => $this->integration->name,
                'favicon_url' => $this->integration->favicon_url ?? $this->integration->favicon,
            ],
        ];
    }
}
