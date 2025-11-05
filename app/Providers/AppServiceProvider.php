<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\GuestProjectRepository;
use App\Repositories\GuestProjectRepositoryInterface;
use App\Repositories\GuestTaskRepository;
use App\Repositories\GuestTaskRepositoryInterface;
use App\Repositories\ProjectRepository;
use App\Repositories\ProjectRepositoryInterface;
use App\Repositories\TaskRepository;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 */
	public function register(): void
	{
		// Bind repository interfaces to implementations
		$this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
		$this->app->bind(GuestTaskRepositoryInterface::class, GuestTaskRepository::class);
		$this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
		$this->app->bind(GuestProjectRepositoryInterface::class, GuestProjectRepository::class);
	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void
	{
		Paginator::useTailwind();

		RateLimiter::for('login', function ($request) {
			return [
				Limit::perMinute(5)->by($request->ip()),
				Limit::perMinute(5)->by((string) $request->input('username')),
			];
		});
	}
}
