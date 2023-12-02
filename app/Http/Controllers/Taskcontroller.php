<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;


class Taskcontroller extends Controller
{
    //
    public function newtask(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:task,title',
            'description' => 'required|string',
            'status' => 'required|string',
        ]);

        $task = Task::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'usertask_id' => Auth::user()->id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $task,
        ]);
    }
public function gettask(Request $request)
{
    $statusFilter = $request->input('status');
    $perPage = 5; // Adjust the number of tasks per page as needed

    if (Auth::user()->usertype === 'admin') {
        $tasks = ($statusFilter)
            ? Task::where('status', $statusFilter)->paginate($perPage)
            : Task::paginate($perPage);

        return response()->json([
            'status' => 'success',
            'tasks' => $tasks,
        ]);
    } else if (Auth::user()->usertype === 'user') {
        $tasks = ($statusFilter)
            ? Task::where('usertask_id', Auth::user()->id)
                  ->where('status', $statusFilter)
                  ->with('user:id,name')
                  ->paginate($perPage)
            : Task::where('usertask_id', Auth::user()->id)
                  ->with('user:id,name')
                  ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'tasks' => $tasks,
        ]);
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'User not found',
        ]);
    }
}

    public function destroy(Task $task)
    {
        try {
            $task->delete();

            return response()->json(['message' => 'Task deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting task'], 500);
        }
    }
    
        public function updateStatus($id)
    {
        // Validate the request data if needed
        // $this->validate($request, [
        //     'status' => 'required|in:complete,incomplete',
        // ]);

        // Find the task by ID
        $task = Task::findOrFail($id);

        // Update the task status
        $task->status = $task->status === 'complete' ? 'incomplete' : 'complete';
        $task->save();

        // You can return a response if needed
        return response()->json(['message' => 'Task status updated successfully']);
    }

}