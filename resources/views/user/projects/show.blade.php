@php
	$isGuest = session('is_guest', false);
	$indexRoute = $isGuest ? route('guest.projects.index') : route('projects.index');
	$updateRoute = $isGuest ? route('guest.projects.update', $project->id) : route('projects.update', $project);
	$deleteRoute = $isGuest ? route('guest.projects.destroy', $project->id) : route('projects.destroy', $project);
	$breadcrumbs = [
		['title' => 'Projects', 'url' => $indexRoute],
		['title' => 'Project Details', 'url' => null]
	];
@endphp

<x-layout.app 
	title="Project Details - Taskware" 
	:user="$user" 
	:breadcrumbs="$breadcrumbs"
	:guest-id="isset($guest_id) ? $guest_id : null"
>
	<!-- Success/Error Messages -->
	<x-form.message type="success" :message="session('success')" />
	<x-form.message type="error" :message="session('error')" />
	
	<div class="max-w-4xl mx-auto space-y-4">
		<!-- Project Header -->
		<div class="border-2 border-primary">
			<div class="px-3 pb-2 pt-1 border-b-2 border-primary bg-header">
				<h1 class="text-2xl font-bold text-primary">{{ $project->title }}</h1>
			</div>

			<!-- Project Details Section -->
			<div class="p-3">
				<form method="POST" action="{{ $updateRoute }}">
					@csrf
					@method('PUT')

					<!-- Title -->
					<div>
						<label for="title" class="block text-lg font-medium text-primary mb-2">
							Title <span class="text-red-500">*</span>
						</label>
						<input 
							type="text" 
							id="title" 
							name="title" 
							required 
							value="{{ old('title', $project->title) }}"
							class="w-full border-2 border-primary px-4 py-3 bg-primary text-primary focus:outline-none focus:border-gray-500"
							placeholder="Enter project title"
						>
						@error('title')
							<div class="text-red-500 text-sm mt-1">{{ $message }}</div>
						@enderror
					</div>

					<!-- Description -->
					<div>
						<label for="description" class="block text-lg font-medium text-primary mb-2 mt-3">
							Description <span class="text-red-500">*</span>
						</label>
						<textarea 
							id="description" 
							name="description" 
							required 
							rows="6"
							class="w-full border-2 border-primary px-4 py-3 bg-primary text-primary focus:outline-none focus:border-gray-500"
							placeholder="Enter project description"
						>{{ old('description', $project->description) }}</textarea>
						@error('description')
							<div class="text-red-500 text-sm mt-1">{{ $message }}</div>
						@enderror
					</div>

					<!-- Created Date (Read-only) -->
					<div>
						<label class="block text-md font-medium text-primary my-2">Created</label>
						<div class="border border-primary px-3 py-2 bg-primary text-primary">
							{{ $project->created_at->format('M j, Y g:i A') }}
						</div>
					</div>

					<!-- Form Actions -->
					<div class="flex justify-between space-x-3 mt-6 border-primary">
						<a 
							href="{{ $indexRoute }}" 
							class="border-2 border-primary px-4 py-2 text-primary hover:bg-secondary hover:text-secondary"
						>
							Cancel
						</a>

						<button 
							type="button"
							onclick="confirmDelete()"
							class="border-2 border-red-500 px-4 py-2 text-red-500 hover:bg-red-500 hover:text-white"
						>
							Delete
						</button>

						<button 
							type="submit"
							class="border-2 border-primary px-4 py-2 text-primary hover:bg-secondary hover:text-secondary font-medium"
						>
							Save
						</button>
					</div>
				</form>
			</div>
		</div>

		<!-- Project Tasks Section -->
		<div class="border-2 border-primary">
			<div class="px-2 py-1 border-b-2 border-primary bg-header">
				<h2 class="text-lg font-medium text-primary flex items-center space-x-2">
					<x-icons.task class="w-5 h-5" />
					<span>Tasks ({{ $project->tasks->count() }})</span>
				</h2>
			</div>

			<div class="p-3">
				@if($project->tasks->count() > 0)
					<!-- Task List -->
					<div class="space-y-3">
						@foreach($project->tasks as $task)
							<div class="border border-primary p-2 hover:bg-gray-300 hover:bg-opacity-50 text-primary hover:text-primary cursor-pointer transition-colors"
								 @if($isGuest)
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
									<div class="flex items-center space-x-2 text-xs">
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
					<div class="text-center py-8">
						<div class="text-primary mb-4">
							<x-icons.task class="mx-auto h-16 w-16" />
						</div>
						<h3 class="text-lg font-medium text-primary mb-2">No tasks in this project</h3>
						<p class="text-primary mb-4">Create tasks and assign them to this project!</p>
						<a 
							href="{{ $isGuest ? route('guest.tasks.create') : route('tasks.create') }}"
							class="inline-block border-2 border-primary px-6 py-2 text-primary hover:bg-secondary hover:text-secondary"
						>
							Create Task
						</a>
					</div>
				@endif
			</div>
		</div>
	</div>

	<!-- Delete Confirmation Modal -->
	<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
		<div class="flex items-center justify-center min-h-screen p-3">
			<div class="bg-primary border-2 border-primary w-full max-w-md">
				<div class="p-6">
					<div class="flex items-center mb-4">
						<x-icons.delete class="w-6 h-6 text-red-500 mr-3" />
						<h3 class="text-lg font-bold text-primary">Delete Project</h3>
					</div>
					<p class="text-primary mb-6">
						Are you sure you want to delete "{{ $project->title }}"? This action cannot be undone.
					</p>
					<div class="flex justify-end space-x-3">
						<button 
							onclick="closeDeleteModal()"
							class="border-2 border-primary px-4 py-2 text-primary hover:bg-secondary hover:text-secondary"
						>
							Cancel
						</button>
						<form method="POST" action="{{ $deleteRoute }}" class="inline">
							@csrf
							@method('DELETE')
							<button 
								type="submit"
								class="border-2 border-red-500 px-4 py-2 text-red-500 hover:bg-red-500 hover:text-white"
							>
								Delete
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<x-slot name="scripts">
		<script>
			function confirmDelete() {
				document.getElementById('deleteModal').classList.remove('hidden');
				document.body.style.overflow = 'hidden';
			}

			function closeDeleteModal() {
				document.getElementById('deleteModal').classList.add('hidden');
				document.body.style.overflow = 'auto';
			}

			// Close modal on escape key
			document.addEventListener('keydown', function(event) {
				if (event.key === 'Escape') {
					closeDeleteModal();
				}
			});
		</script>
	</x-slot>
</x-layout.app>

