<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $task = Task::all();
        return response()->json($task);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|unique:tasks,title',
        ]);

        $task = new Task();
        $task->title =  $request->title;
        $task->save();
        return response()->json($task);
    }

    public function updatetask(Request $request, $taskId)
    {
        $request->validate([
            'title' => 'required|string|unique:tasks,title,' . $taskId,
        ]);

        $task = Task::find($taskId);
        $task->title = $request->title;
        $task->save();

        return response()->json($task);
    }

    public function updatecheck(Request $request, $taskId)
    {
        $task = Task::find($taskId);
        $task->completed = $request->completed ? 1 : 0;
        $task->save();

        return response()->json($task);
    }

    public function destroy($taskId)
    {
        $task = Task::find($taskId);
        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }
}