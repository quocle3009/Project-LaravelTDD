<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskService extends Controller
{
    protected $task;  

    public function __construct(Task $task)  
    {  
        $this->task = $task;  
    }  

    public function getAllTasks()  
    {  
        return $this->task->latest('id')->paginate(10);  
    }  

    public function createTask(array $data)  
    {  
        return $this->task->create($data);  
    }  

    public function getTaskById($id)  
    {  
        return $this->task->findOrFail($id);  
    }  

    public function updateTask(Task $task, array $data)  
    {  
        $task->update($data);  
        return $task;  
    }  

    public function deleteTask(Task $task)  
    {  
        $task->delete();  
    }  

    public function buildTaskQuery(Request $request)  
    {  
        $allowedSortColumns = ['id', 'name', 'content'];  
        $sortColumn = in_array($request->get('sort_column', 'id'), $allowedSortColumns) ? $request->get('sort_column') : 'id';  

        $allowedSortOrders = ['asc', 'desc'];  
        $sortOrder = in_array(strtolower($request->get('sort_order', 'desc')), $allowedSortOrders) ? $request->get('sort_order') : 'desc';  

        return Task::query()  
            ->when($request->input('query'), function ($q, $query) {  
                return $q->where('name', 'like', "%{$query}%");  
            })  
            ->when($request->input('project_id'), function ($q, $projectId) {  
                return $q->where('project_id', $projectId);  
            })  
            ->with('project')  
            ->orderBy($sortColumn, $sortOrder)  
            ->latest('id');  
    }  
}
