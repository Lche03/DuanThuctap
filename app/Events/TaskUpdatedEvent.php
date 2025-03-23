<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $message;

    public function __construct(Task $task, $message)
    {
        $this->task = $task;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('tasks');
    }

    public function broadcastWith()
    {
        return [
            'task' => $this->task,
            'message' => $this->message,
        ];
    }
}
