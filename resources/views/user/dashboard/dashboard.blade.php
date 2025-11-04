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
		<!-- Header with Quick Add Button -->
		<div class="flex justify-between items-center">
			<button 
				onclick="openTaskModal()"
				class="border-2 border-primary px-4 py-2 text-primary hover:bg-secondary hover:text-secondary flex items-center space-x-2"
			>
				<x-icons.plus class="w-5 h-5" />
				<span>Quick Add</span>
			</button>
		</div>

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
						<button 
							onclick="openTaskModal()"
							class="border-2 border-primary px-6 py-2 text-primary hover:bg-secondary hover:text-secondary"
						>
							Create Your First Task
						</button>
					</div>
				@endif
			</div>
		</div>
	</div>

	<!-- Task Modal -->
	<x-features.task.task-modal />

	<x-slot name="scripts">
		<script>
			// Set global variable for guest mode
			window.isGuest = {{ session('is_guest') ? 'true' : 'false' }};
		</script>
	</x-slot>
</x-layout.app>