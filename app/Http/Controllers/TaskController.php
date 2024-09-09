<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteTaskRequest;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\DeleteTasksRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function view(): Factory|Application|View
    {
        Auth::login(User::first());

        return view('app');
    }

    public function getUserTasks(): JsonResponse
    {
        return response()->json(Task::with('user')->get());
    }

    public function create(CreateTaskRequest $request): JsonResponse
    {
        Auth::user()->tasks()->create([
            'description' => $request->get('description'),
            'estimated_time' => $request->get('estimated_time'),
            'created_date' => now(),
        ]);

        return response()->json(true);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {

        $task->update([
            'description' => $request->get('description'),
            'estimated_time' => $request->get('estimated_time'),
            'used_time' => $request->get('used_time'),
            'user_id' => $request->get('assignee')['id'],
        ]);

        return response()->json(true);
    }

    public function delete(DeleteTasksRequest $request): JsonResponse
    {
        Task::destroy($request->get('idsToDelete'));

        return response()->json(true);
    }

    public function completeTasks(CompleteTaskRequest $request): JsonResponse
    {
        Task::whereIn('id', $request->get('idsToComplete'))->where('completed_date', null)->update(['completed_date' => now()]);

        return response()->json(true);
    }
}
