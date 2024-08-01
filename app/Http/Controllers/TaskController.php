<?php  

namespace App\Http\Controllers;  

use App\Http\Requests\TaskRequest;  
use App\Models\Project;  
use App\Services\TaskService; // Import TaskService  
use Illuminate\Http\Request;  
use Illuminate\Http\Response;  
use Illuminate\Support\Facades\Log;  
use App\Models\Task;
class TaskController extends Controller  
{  
    protected $taskService;  

    public function __construct(TaskService $taskService)  
    {  
        $this->taskService = $taskService; // Inject TaskService  
    }  

    public function index()  
    {  
        $projects = Project::all();  
        $tasks = $this->taskService->getAllTasks(); // Fetch all tasks using TaskService  
        return view('tasks.index', compact('tasks', 'projects'));  
    }  

    public function create()  
    {  
        $projects = Project::all();  
        return response()->json(['projects' => $projects]);  
    }  

    public function store(TaskRequest $request)  
    {  
        $this->taskService->createTask($request->validated()); // Use TaskService to create task  
        return response()->json(['success' => true]);  
    }  

    public function edit(Task $task)  
    {  
        $projects = Project::all();  
        return response()->json(['task' => $task, 'projects' => $projects]);  
    }  

    public function update(TaskRequest $request, Task $task)  
    {  
        $this->taskService->updateTask($task, $request->validated()); // Update task via TaskService  
        return response()->json(['message' => 'Task updated successfully']);  
    }  

    public function destroy(Task $task)  
    {  
        try {  
            $this->taskService->deleteTask($task); // Delete task via TaskService  
            return response()->json(['success' => true]);  
        } catch (\Exception $e) {  
            Log::error('Delete error: ' . $e->getMessage());  
            return response()->json(['error' => 'An error occurred'], 500);  
        }  
    }  

    public function search(Request $request)  
    {  
        try {  
            $tasks = $this->taskService->buildTaskQuery($request)->paginate(10, ['*'], 'page', $request->input('page', 1)); // Use TaskService for querying  
            return response()->json([  
                'data' => $tasks->items(),  
                'links' => $tasks->appends($request->all())->links()->toHtml(),  
            ]);  
        } catch (\Exception $e) {  
            Log::error('Search error: ' . $e->getMessage());  
            return response()->json(['error' => 'An error occurred'], 500);  
        }  
    }  
}  