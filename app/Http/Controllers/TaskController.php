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

    public function index(Request $request)
    {
        $query = $this->task->query();

        if ($search = $request->query('search')) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('content', 'like', '%' . $search . '%');
        }

        $tasks = $query->latest('id')->paginate(10);
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


    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
    
        // Chuyển hướng người dùng về trang trước đó với anchor
        return redirect()->back()->with('success', 'Task deleted successfully.')->withAnchor('#task-' . $id);
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

}
