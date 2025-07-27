<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreWorkspaceRequest;
use App\Http\Requests\UpdateWorkspaceRequest;
use App\Repositories\Contracts\WorkspaceRepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use Inertia\Response;

class WorkspaceController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly WorkspaceRepositoryInterface $workspaces
    ) {
    }

    public function index(Request $request): Response
    {
        $items = $this->workspaces->allForUser($request->user());

        return Inertia::render('workspaces/Index', [
            'workspaces' => $items,
        ]);
    }

    public function store(StoreWorkspaceRequest $request)
    {
        $workspace = $this->workspaces->create($request->validated(), $request->user());

        return redirect()->route('workspaces.index')->with('success', 'Workspace created.');
    }

    public function show(Request $request, int $id): Response
    {
        // $workspace = $this->workspaces->findForUser($request->user(), $id);
        // $this->authorize('view', $workspace);

        // return Inertia::render('workspaces/Show', [
        //     'workspace' => $workspace,
        // ]);

        $workspace = $this->workspaces->findForUser($request->user(), $id);

        $this->authorize('view', $workspace);

        $tasks = $workspace->tasks()
            ->orderByDesc('id')
            ->get(['id', 'title', 'description', 'is_completed', 'completed_at', 'deadline', 'workspace_id', 'created_at']);

        return Inertia::render('workspaces/Show', [
            'workspace' => $workspace->only(['id', 'name']),
            'tasks' => $tasks,
        ]);
    }

    public function update(UpdateWorkspaceRequest $request, int $id)
    {
        $workspace = $this->workspaces->findForUser($request->user(), $id);
        $this->authorize('update', $workspace);

        $this->workspaces->update($workspace, $request->validated());

        return redirect()->route('workspaces.index')->with('success', 'Workspace updated.');
    }

    public function destroy(Request $request, int $id)
    {
        $workspace = $this->workspaces->findForUser($request->user(), $id);
        $this->authorize('delete', $workspace);

        $this->workspaces->delete($workspace);

        return redirect()->route('workspaces.index')->with('success', 'Workspace deleted.');
    }

}
