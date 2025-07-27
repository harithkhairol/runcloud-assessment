<?php

namespace App\Providers;

use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\WorkspaceRepositoryInterface;
use App\Repositories\Eloquent\TaskRepository;
use App\Repositories\Eloquent\WorkspaceRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            WorkspaceRepositoryInterface::class,
            WorkspaceRepository::class
        );

        $this->app->bind(
            TaskRepositoryInterface::class, 
            TaskRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
