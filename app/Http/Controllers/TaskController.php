<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Subtask;
use App\Services\LivePollsService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $livePollsService;

    public function __construct(LivePollsService $livePollsService)
    {
        $this->livePollsService = $livePollsService;
    }

    public function index()
    {
        $tasks = Task::with('subtasks')->orderBy('priority', 'desc')->get();
        if (request()->wantsJson()) {
            return response()->json(['tasks' => $tasks]);
        }
        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:255'
        ]);

        $task = Task::create([
            'text' => $validated['text'],
            'completed' => false,
            'priority' => 0
        ]);

        $task->load('subtasks');
        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'text' => 'string|max:255',
            'completed' => 'boolean',
            'priority' => 'integer'
        ]);

        $task->update($validated);
        $task->load('subtasks');
        
        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task
        ]);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }

    public function expand(Task $task)
    {
        try {
            $expanded = $this->livePollsService->generateResponse($task->text, true);
            $task->update(['expanded' => $expanded]);
            $task->load('subtasks');
            
            return response()->json([
                'message' => 'Task expanded successfully',
                'task' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to expand task'
            ], 500);
        }
    }

    public function prioritize()
    {
        $tasks = Task::where('completed', false)->get();
        
        try {
            $taskTexts = $tasks->pluck('text')->join("\n");
            $response = $this->livePollsService->generateResponse($taskTexts, false);
            $priorities = explode(',', $response);
            
            foreach ($tasks as $index => $task) {
                $task->update(['priority' => (int) $priorities[$index]]);
            }

            $updatedTasks = Task::with('subtasks')->orderBy('priority', 'desc')->get();
            
            return response()->json([
                'message' => 'Tasks prioritized successfully',
                'tasks' => $updatedTasks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to prioritize tasks'
            ], 500);
        }
    }
}