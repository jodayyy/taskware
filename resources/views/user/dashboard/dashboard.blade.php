@php
	$breadcrumbs = [
		['title' => 'Dashboard', 'url' => null]
	];
@endphp

<x-layout.app 
	title="Dashboard - Taskware" 
	:user="$user" 
	:breadcrumbs="$breadcrumbs"
	:guest-id="isset($guest_id) ? $guest_id : null"
>
	<!-- Success/Error Messages -->
	<x-form.message type="success" :message="session('success')" />
	<x-form.message type="error" :message="session('error')" />
	
	<div class="space-y-6">

		<!-- Recent Tasks Section -->
		<div class="border-2 border-primary">
			<div class="p-4 border-b-2 border-primary">
				<div class="flex justify-between items-center">
					<h2 class="text-lg font-medium text-primary flex items-center space-x-2">
						<x-icons.task class="w-5 h-5" />
						<span>Recent Tasks</span>
					</h2>
					@if(isset($tasks) && $tasks->count() > 0)
						@if(session('is_guest'))
							<a href="{{ route('guest.tasks.index') }}" class="text-sm text-primary hover:underline">
								View All Tasks
							</a>
						@else
							<a href="{{ route('tasks.index') }}" class="text-sm text-primary hover:underline">
								View All Tasks
							</a>
						@endif
					@endif
				</div>
			</div>

			<div class="p-4">
				@if(isset($tasks) && $tasks->count() > 0)
					<!-- Task List -->
					<div class="space-y-3">
						@foreach($tasks as $task)
							<div class="border border-primary p-2 hover:bg-gray-300 hover:bg-opacity-50 text-primary hover:text-primary cursor-pointer transition-colors"
								 @if(session('is_guest'))
									 onclick="location.href='{{ route('guest.tasks.task-details', $task->id) }}'"
								 @else
									 onclick="location.href='{{ route('tasks.show', $task) }}'"
								 @endif>
								<div class="flex-row md:flex justify-between items-start">
									<!-- Left Side: Title and Description -->
									<div class="flex-1 pr-4">
										<h3 class="font-medium mb-1">{{ $task->title }}</h3>
									</div>
									<!-- Right Side: Due Date, Priority, Status -->
									<div class="flex items-center space-x-4 text-xs">
										<span class="flex items-center space-x-1">
											<span>Due:</span>
											<span class="font-medium">
												@if(is_string($task->deadline))
													{{ \Carbon\Carbon::parse($task->deadline)->format('M j, Y') }}
												@else
													{{ $task->deadline->format('M j, Y') }}
												@endif
											</span>
										</span>
										<span class="px-2 py-1 border border-current">
											{{ $task->priority_label }}
										</span>
										<span class="px-2 py-1 border border-current">
											{{ $task->status_label }}
										</span>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				@else
					<!-- Empty State -->
					<div class="text-center py-12">
						<div class="text-primary mb-4">
							<x-icons.task class="mx-auto h-16 w-16" />
						</div>
						<h3 class="text-lg font-medium text-primary mb-2">No tasks yet</h3>
						<p class="text-primary mb-4">Create your first task to get started!</p>
						<a 
							href="{{ session('is_guest') ? route('guest.tasks.create') : route('tasks.create') }}"
							class="inline-block border-2 border-primary px-6 py-2 text-primary hover:bg-secondary hover:text-secondary"
						>
							Create Your First Task
						</a>
					</div>
				@endif
			</div>
		</div>

		<!-- Recent Projects Section -->
		<div class="border-2 border-primary">
			<div class="p-4 border-b-2 border-primary">
				<div class="flex justify-between items-center">
					<h2 class="text-lg font-medium text-primary flex items-center space-x-2">
						<x-icons.project class="w-5 h-5" />
						<span>Recent Projects</span>
					</h2>
					@if(isset($projects) && $projects->count() > 0)
						@if(session('is_guest'))
							<a href="{{ route('guest.projects.index') }}" class="text-sm text-primary hover:underline">
								View All Projects
							</a>
						@else
							<a href="{{ route('projects.index') }}" class="text-sm text-primary hover:underline">
								View All Projects
							</a>
						@endif
					@endif
				</div>
			</div>

			<div class="p-4">
				@if(isset($projects) && $projects->count() > 0)
					<!-- Project List -->
					<div class="space-y-3">
						@foreach($projects as $project)
							<div class="border border-primary p-2 hover:bg-gray-300 hover:bg-opacity-50 text-primary hover:text-primary cursor-pointer transition-colors"
								 @if(session('is_guest'))
									 onclick="location.href='{{ route('guest.projects.show', $project->id) }}'"
								 @else
									 onclick="location.href='{{ route('projects.show', $project) }}'"
								 @endif>
								<div class="flex-row md:flex justify-between items-start">
									<!-- Left Side: Title -->
									<div class="flex-1 pr-4">
										<h3 class="font-medium mb-1">{{ $project->title }}</h3>
									</div>
									<!-- Right Side: Created Date -->
									<div class="flex items-center space-x-4 text-xs">
										<span class="flex items-center space-x-1">
											<span>Created:</span>
											<span class="font-medium">
												{{ $project->created_at->format('M j, Y') }}
											</span>
										</span>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				@else
					<!-- Empty State -->
					<div class="text-center py-12">
						<div class="text-primary mb-4">
							<x-icons.project class="mx-auto h-16 w-16" />
						</div>
						<h3 class="text-lg font-medium text-primary mb-2">No projects yet</h3>
						<p class="text-primary mb-4">Create your first project to get started!</p>
						<a 
							href="{{ session('is_guest') ? route('guest.projects.create') : route('projects.create') }}"
							class="inline-block border-2 border-primary px-6 py-2 text-primary hover:bg-secondary hover:text-secondary"
						>
							Create Your First Project
						</a>
					</div>
				@endif
			</div>
		</div>
	</div>
</x-layout.app>