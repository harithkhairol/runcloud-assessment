<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Collection;

interface WorkspaceRepositoryInterface
{
    public function allForUser(User $user): Collection;

    public function findForUser(User $user, int $id): Workspace;

    public function create(array $data, User $user): Workspace;

    public function update(Workspace $workspace, array $data): Workspace;

    public function delete(Workspace $workspace): void;
}
