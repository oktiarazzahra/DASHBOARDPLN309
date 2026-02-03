<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DataUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $dataType;
    public $year;
    public $updateCount;
    public $statistics;

    /**
     * Create a new event instance.
     */
    public function __construct($dataType, $year, $updateCount, $statistics = null)
    {
        $this->dataType = $dataType;
        $this->year = $year;
        $this->updateCount = $updateCount;
        $this->statistics = $statistics;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('dashboard-updates');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'data.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'dataType' => $this->dataType,
            'year' => $this->year,
            'updateCount' => $this->updateCount,
            'statistics' => $this->statistics,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
