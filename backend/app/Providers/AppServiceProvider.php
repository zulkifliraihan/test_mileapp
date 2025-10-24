<?php

namespace App\Providers;

use App\Http\Repositories\UserRepository\UserInterface;
use App\Http\Repositories\UserRepository\UserRepository;
use App\Http\Repositories\TaskRepository\TaskInterface;
use App\Http\Repositories\TaskRepository\TaskRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind interfaces to concrete implementations
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(TaskInterface::class, TaskRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
