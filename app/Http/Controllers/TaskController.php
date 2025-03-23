<?php

namespace App\Http\Controllers;

use App\Events\TaskAdded;
use App\Events\TaskUpdated;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        return Task::with('assignee:id,name') // Lấy thông tin assignee
            ->where('creator_id', auth()->id())
            ->orWhere('assignee_id', auth()->id())
            ->get();
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Bạn không có quyền thêm công việc'], 403);
        }

        $task = Task::create($request->all() + ['creator_id' => auth()->id()]);
        event(new TaskAdded($task));
        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        // Chỉ admin hoặc assignee được sửa task
        if (auth()->user()->role !== 'admin' && auth()->id() !== $task->assignee_id) {
            return response()->json(['message' => 'Bạn không có quyền sửa công việc này'], 403);
        }

        // Nếu không phải admin, chỉ được sửa trạng thái    
        if (auth()->user()->role !== 'admin') {
            $task->update(['status' => $request->status]);
        } else {
            $task->update($request->all());
        }
        event(new TaskUpdated($task));
        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }

    public function users()
    {
        return User::all();
    }
    public function changeStatus(Request $request, Task $task)
    {
        $this->authorize('update', $task); 

        $task->status = $request->status;
        $task->save();

        return response()->json(['message' => 'Trạng thái công việc đã được cập nhật.']);
    }
}
