<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\WorkspaceRepositoryInterface;

class WorkspaceRepository implements WorkspaceRepositoryInterface
{
    public function allForUser(User $user): Collection
    {
        return $user->workspaces()->latest()->get();
    }

    public function findForUser(User $user, int $id): Workspace
    {
        return $user->workspaces()->findOrFail($id);
    }

    public function create(array $data, User $user): Workspace
    {
        return $user->workspaces()->create($data);
    }

    public function update(Workspace $workspace, array $data): Workspace
    {
        $workspace->update($data);
        return $workspace;
    }

    public function delete(Workspace $workspace): void
    {
        $workspace->delete();
    }
}
