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


    // Trong TasksController.php hoặc controller tương ứng
    public function index(Request $request)
    {
        $search = $request->input('search');

        $tasks = Task::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('content', 'like', '%' . $search . '%');
        })->latest('id')->paginate(5);

        return view('tasks.index', compact('tasks', 'search'));
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
    public function search(Request $request)
    {
        $query = $request->input('query');

        // Thực hiện tìm kiếm trên tất cả các trang
        $results = Task::where('name', 'like', '%'.$query.'%')
                       ->orWhere('content', 'like', '%'.$query.'%')
                       ->latest('id')
                       ->paginate(10);

        return response()->json($results);
    }

}
