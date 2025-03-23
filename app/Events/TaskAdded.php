<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TaskAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
        Log::info('🔥 TaskAdded Event Created - Task ID: ' . $task->id);
    }

    public function broadcastOn()
    {
        Log::info('📡 Broadcasting to Channel: tasks');
        return new Channel('tasks');
    }
    public function broadcastAs()
    {
        return 'task.added'; // và 'TaskUpdated'
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->task->id,
            'title' => $this->task->title,
            'assignee_id' => $this->task->assignee_id,
            'status' => $this->task->status,
        ];
    }
}
