<?php

namespace App\Repositories\Contracts;

use App\Models\Task;
use App\Models\Workspace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    public function forWorkspace(Workspace $workspace, int $perPage = 15): LengthAwarePaginator;
    public function findInWorkspace(Workspace $workspace, int $taskId): Task;
    public function create(Workspace $workspace, array $data): Task;
    public function update(Task $task, array $data): Task;
    public function delete(Task $task): void;
    public function toggleComplete(Task $task): Task;
}
