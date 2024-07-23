<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function index()
    {
        $tasks = $this->task->latest('id')->paginate(10);
        return view('tasks.index', compact('tasks'));
    }


    public function store(CreateTaskRequest $request)
    {
        $this->task->create($request->all());
        return redirect()->route('tasks.index');
    }



    public function create()
    {
        return view('tasks.create');
    }


    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }


    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|max:255',
            'content' => 'nullable',
        ]);

        $task->update([
            'name' => $request->input('name'),
            'content' => $request->input('content'),
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }
    // TaskController.php
    public function search(Request $request)
    {
        try {
            $query = $request->input('query', '');
            $page = $request->input('page', 1);

            if (is_null($query)) {
                return response()->json(['error' => 'Query parameter is missing'], 400);
            }

            // Kiểm tra dữ liệu đầu vào
            if (empty($query)) {
                return response()->json(['error' => 'Query parameter is empty'], 400);
            }

            $tasks = Task::where('name', 'like', "%$query%")->latest('id')->paginate(10, ['*'], 'page', $page);

            return response()->json([
                'data' => $tasks->items(),
                'links' => $tasks->links()->toHtml()
            ]);
        } catch (\Exception $e) {
            \Log::error('Search error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }


}
