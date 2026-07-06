<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = Task::where('user_id', $request->user()->id)
        ->filter($request->only('status', 'from', 'to'))
        ->latest()
        ->paginate(10);
        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'status' => 'in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);
        $task = Task::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 'pending',
            'due_date' => $request->due_date,
            
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task created Sucessfully',
            'data' => $task
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,$id)
    {
        $task = Task::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();
        if (!$task) {
            return response()->json([
                'message' => 'Forbidden or Not Found'
            ], 403);
        }
        return response()->json([
            'success' => true,
            'data' => $task
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $task = Task::where('id', $id)
        ->where('user_id', $request->user()->id)
        ->first();

    if (!$task) {
        return response()->json([
            'message' => 'Forbidden'
        ], 403);
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'in:pending,in_progress,completed',
        'due_date' => 'nullable|date|after_or_equal:today',
    ]);

    $task->update([
        'title' => $request->title,
        'description' => $request->description,
        'status' => $request->status ?? $task->status,
        'due_date' => $request->due_date,
    ]);

    return response()->json([
        'message' => 'Task updated successfully',
        'success' => true,
        'data' => $task
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,$id)
    {
         $task = Task::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();
        if (!$task) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }
        $task->delete();
        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    
    }
}
