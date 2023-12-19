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
        $this->task=$task;
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


    public function update(Request $request, Task $task) {
        $task->update([
            'name' => $request->input('name'),
            'content' => $request->input('content'),
        ]);

        // Redirect về trang index của tasks với thông báo thành công
        return redirect()->route('tasks.index')->with('status', 'Task updated successfully.');

    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index');
    }
}
