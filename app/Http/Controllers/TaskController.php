<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        return view('todo');
    }

    public function store(Request $request)
    {
        $request->validate([
            'task' => 'required|unique:tasks,task|max:255',
        ]);

        Task::create([
            'task' => $request->task,
        ]);

        return response()->json(['message' => 'Task Added Successfully']);
    }

    public function destroy($id)
    {
        Task::findOrFail($id)->delete();
        return response()->json(['message' => 'Task Deleted Successfully']);
    }

    public function toggle($id)
    {
        $task = Task::findOrFail($id);
        $task->completed = !$task->completed;
        $task->save();

        return response()->json(['message' => 'Task Updated Successfully']);
    }

    public function showAll()
    {
        $tasks = Task::all();
        return response()->json($tasks);
    }
}
