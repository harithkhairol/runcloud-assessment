<?php

namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Models\Workspace;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskRepository implements TaskRepositoryInterface
{
    public function forWorkspace(Workspace $workspace, int $perPage = 15): LengthAwarePaginator
    {
        return Task::where('workspace_id', $workspace->id)
            ->latest()
            ->paginate($perPage);
    }

    public function findInWorkspace(Workspace $workspace, int $taskId): Task
    {
        return Task::where('workspace_id', $workspace->id)->findOrFail($taskId);
    }

    public function create(Workspace $workspace, array $data): Task
    {
        $data['workspace_id'] = $workspace->id;
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

    public function toggleComplete(Task $task): Task
    {
        $task->is_completed = ! $task->is_completed;
        $task->completed_at = $task->is_completed
            ? now(config('app.timezone')) // set timestamp when completed
            : null;                       // reset when marked incomplete

        $task->save();

        return $task;
    }
}
