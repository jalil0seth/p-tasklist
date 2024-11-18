<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Subtask;
use App\Services\LivePollsService;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
    protected $livePollsService;

    public function __construct(LivePollsService $livePollsService)
    {
        $this->livePollsService = $livePollsService;
    }

    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:255'
        ]);

        $subtask = $task->subtasks()->create([
            'text' => $validated['text'],
            'completed' => false
        ]);

        $task->load('subtasks');
        
        return response()->json([
            'message' => 'Subtask added successfully',
            'task' => $task
        ]);
    }

    public function update(Request $request, Task $task, Subtask $subtask)
    {
        $validated = $request->validate([
            'text' => 'string|max:255',
            'completed' => 'boolean'
        ]);

        $subtask->update($validated);
        $task->load('subtasks');
        
        return response()->json([
            'message' => 'Subtask updated successfully',
            'task' => $task
        ]);
    }

    public function destroy(Task $task, Subtask $subtask)
    {
        $subtask->delete();
        $task->load('subtasks');
        
        return response()->json([
            'message' => 'Subtask deleted successfully',
            'task' => $task
        ]);
    }

    public function expand(Task $task, Subtask $subtask)
    {
        try {
            $expanded = $this->livePollsService->generateResponse($subtask->text, true);
            $subtask->update(['expanded' => $expanded]);
            $task->load('subtasks');
            
            return response()->json([
                'message' => 'Subtask expanded successfully',
                'task' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to expand subtask'
            ], 500);
        }
    }
}