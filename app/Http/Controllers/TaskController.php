<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Models\Project;
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
        $projects = Project::all();
        $tasks = $this->task->latest('id')->paginate(10);
        return view('tasks.index', compact('tasks', 'projects'));
    }

    public function create()
    {
        $projects = Project::all();
        return response()->json(['projects' => $projects]);
    }

    public function store(TaskRequest $request)
    {
        $this->task->create($request->all());
        return response()->json(['success' => true]);
    }

    public function edit(Task $task)
    {
        $projects = Project::all();
        return response()->json(['task' => $task, 'projects' => $projects]);
    }

    public function update(TaskRequest $request, Task $task)
    {

        $this->$task->update($request->all());

        return response()->json(['success' => true]);
    }

    public function destroy(Task $task)
    {
        try {
            $task->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Delete error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->input('query', '');
            $projectId = $request->input('project_id', '');
            $page = $request->input('page', 1);
            $sortColumn = $request->get('sort_column', 'id');
            $sortOrder = $request->get('sort_order', 'desc');

            $allowedSortColumns = ['id', 'name', 'content'];
            if (!in_array($sortColumn, $allowedSortColumns)) {
                $sortColumn = 'id';
            }

            $allowedSortOrders = ['asc', 'desc'];
            if (!in_array(strtolower($sortOrder), $allowedSortOrders)) {
                $sortOrder = 'desc';
            }

            $tasksQuery = Task::query()
                ->when($query, function ($q) use ($query) {
                    return $q->where('name', 'like', "%{$query}%");
                })
                ->when($projectId, function ($q) use ($projectId) {
                    return $q->where('project_id', $projectId);
                })
                ->with('project')
                ->orderBy($sortColumn, $sortOrder);

            if (!empty($projectId)) {
                $tasksQuery->where('project_id', $projectId);
            }

            $tasks = $tasksQuery->orderBy($sortColumn, $sortOrder)
                ->latest('id')
                ->paginate(10, ['*'], 'page', $page);

            return response()->json([
                'data' => $tasks->items(),
                'links' => $tasks->appends([
                    'query' => $query,
                    'project_id' => $projectId,
                    'sort_column' => $sortColumn,
                    'sort_order' => $sortOrder
                ])->links()->toHtml()
            ]);
        } catch (\Exception $e) {
            \Log::error('Search error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }
}
