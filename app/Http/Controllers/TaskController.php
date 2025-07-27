<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Workspace;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private TaskRepositoryInterface $tasks) {}

    public function index(Workspace $workspace)
    {
        $this->authorize('view', $workspace); // You can also have WorkspacePolicy@view
        return Inertia::render('tasks/Index', [
            'workspace' => $workspace->only(['id', 'name']),
            'tasks' => $this->tasks->forWorkspace($workspace),
        ]);
    }

    public function store(StoreTaskRequest $request, Workspace $workspace)
    {
        $this->authorize('create', [Task::class, $workspace]);

        $this->tasks->create($workspace, $request->validated());

        return back()->with('success', 'Task created.');
    }

    public function show(Workspace $workspace, int $task)
    {
        $task = $this->tasks->findInWorkspace($workspace, $task);
        $this->authorize('view', $task);

        return Inertia::render('tasks/Show', [
            'workspace' => $workspace->only(['id', 'name']),
            'task' => $task,
        ]);
    }

    public function update(UpdateTaskRequest $request, Workspace $workspace, int $task)
    {
        $task = $this->tasks->findInWorkspace($workspace, $task);
        $this->authorize('update', $task);

        $this->tasks->update($task, $request->validated());

        return back()->with('success', 'Task updated.');
    }

    public function destroy(Workspace $workspace, int $task)
    {
        $task = $this->tasks->findInWorkspace($workspace, $task);
        $this->authorize('delete', $task);

        $this->tasks->delete($task);

        return redirect()
            ->route('workspaces.tasks.index', $workspace)
            ->with('success', 'Task deleted.');
    }

    public function toggleComplete(Workspace $workspace, int $task)
    {
        $task = $this->tasks->findInWorkspace($workspace, $task);
        $this->authorize('toggle', $task);

        $this->tasks->toggleComplete($task);

        return back()->with('success', 'Task status updated.');
    }
}
