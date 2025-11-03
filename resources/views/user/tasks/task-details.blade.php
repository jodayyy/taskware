@php
	$isGuest = session('is_guest', false);
	$updateRoute = $isGuest ? route('guest.tasks.update', $task->id) : route('tasks.update', $task);
	$deleteRoute = $isGuest ? route('guest.tasks.destroy', $task->id) : route('tasks.destroy', $task);
	$dashboardRoute = $isGuest ? route('guest.dashboard') : route('dashboard');
	$tasksRoute = $isGuest ? route('guest.tasks.index') : route('tasks.index');
	
	$breadcrumbs = [
		['title' => 'Tasks', 'url' => $tasksRoute],
		['title' => 'Task Details', 'url' => null]
	];
@endphp

<x-layout.app 
	title="Task Details - Taskware" 
	:user="$user" 
	:breadcrumbs="$breadcrumbs"
	:guest-id="isset($guest_id) ? $guest_id : null"
>
	<!-- Success/Error Messages -->
	<x-form.message type="success" :message="session('success')" />
	<x-form.message type="error" :message="session('error')" />
	
	<div class="max-w-4xl mx-auto space-y-4">
		<!-- Task Header -->
		<div class="border-2 border-primary">
			<div class="p-4 border-b-2 border-primary">
				<div class="flex-row md:flex justify-between items-start">
					<h1 class="text-2xl font-bold text-primary">{{ $task->title }}</h1>
					<div class="flex items-center space-x-4 text-sm text-primary">
						<span class="flex items-center space-x-1">
							<span>Due:</span>
							<span class="font-medium">{{ $task->deadline->format('M j, Y') }}</span>
						</span>
						<span class="px-3 py-1 border-2 border-primary">
							{{ $task->priority_label }}
						</span>
						<span class="px-3 py-1 border-2 border-primary">
							{{ $task->status_label }}
						</span>
					</div>
				</div>
			</div>

			<!-- Task Details Section -->
			<div class="p-4">
				<form method="POST" action="{{ $updateRoute }}" class="space-y-2">
					@csrf
					@method('PUT')

					<!-- Title -->
					<div>
						<label for="title" class="block text-lg font-medium text-primary mb-3">
							Title <span class="text-red-500">*</span>
						</label>
						<input 
							type="text" 
							id="title" 
							name="title" 
							required 
							value="{{ old('title', $task->title) }}"
							class="w-full border-2 border-primary px-4 py-3 bg-primary text-primary focus:outline-none focus:border-gray-500"
							placeholder="Enter task title"
						>
						@error('title')
							<div class="text-red-500 text-sm mt-1">{{ $message }}</div>
						@enderror
					</div>

					<!-- Description -->
					<div>
						<label for="description" class="block text-lg font-medium text-primary mb-3">
							Description <span class="text-red-500">*</span>
						</label>
						<textarea 
							id="description" 
							name="description" 
							required 
							rows="4"
							class="w-full border-2 border-primary px-4 py-3 bg-primary text-primary focus:outline-none focus:border-gray-500"
							placeholder="Enter task description"
						>{{ old('description', $task->description) }}</textarea>
						@error('description')
							<div class="text-red-500 text-sm mt-1">{{ $message }}</div>
						@enderror
					</div>

					<!-- Task Information Grid -->
					<div>
						<h3 class="text-lg font-medium text-primary mb-3">Task Information</h3>
						<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
							<!-- Deadline -->
							<div>
								<x-form.date-picker 
									name="deadline" 
									id="deadline"
									label="Deadline" 
									:required="true"
									:value="old('deadline', $task->deadline->format('d/m/Y'))"
								/>
								@error('deadline')
									<div class="text-red-500 text-sm mt-1">{{ $message }}</div>
								@enderror
							</div>

							<!-- Priority -->
							<div>
								<label for="priority" class="block text-sm font-medium text-primary mb-2">
									Priority <span class="text-red-500">*</span>
								</label>
								<x-form.custom-select 
									name="priority"
									id="priority"
									:value="old('priority', $task->priority)"
									:options="[
										'urgent' => 'Urgent',
										'normal' => 'Normal',
										'low' => 'Low'
									]"
									placeholder="Select Priority"
									required
								/>
								@error('priority')
									<div class="text-red-500 text-sm mt-1">{{ $message }}</div>
								@enderror
							</div>

							<!-- Status -->
							<div>
								<label for="status" class="block text-sm font-medium text-primary mb-2">
									Status <span class="text-red-500">*</span>
								</label>
								<x-form.custom-select 
									name="status"
									id="status"
									:value="old('status', $task->status)"
									:options="[
										'to_do' => 'To Do',
										'in_progress' => 'In Progress',
										'done' => 'Done'
									]"
									placeholder="Select Status"
									required
								/>
								@error('status')
									<div class="text-red-500 text-sm mt-1">{{ $message }}</div>
								@enderror
							</div>

							<!-- Created Date (Read-only) -->
							<div>
								<label class="block text-sm font-medium text-primary mb-2">Created</label>
								<div class="border border-primary px-3 py-2 bg-gray-50 text-primary">
									{{ $task->created_at->format('M j, Y g:i A') }}
								</div>
							</div>
						</div>
					</div>

					<!-- Notes Section -->
					<div>
						<label for="notes" class="block text-lg font-medium text-primary mb-2">
							Notes (Optional)
						</label>
						<textarea 
							id="notes" 
							name="notes" 
							rows="3"
							class="w-full border-2 border-primary px-4 py-3 bg-primary text-primary focus:outline-none focus:border-gray-500"
							placeholder="Additional notes"
						>{{ old('notes', $task->notes) }}</textarea>
						@error('notes')
							<div class="text-red-500 text-sm mt-1">{{ $message }}</div>
						@enderror
					</div>

					<!-- Form Actions -->
					<div class="flex justify-between space-x-3 pt-4 border-primary">
						<a 
							href="{{ $tasksRoute }}" 
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
	</div>

	<!-- Delete Confirmation Modal -->
	<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
		<div class="flex items-center justify-center min-h-screen p-4">
			<div class="bg-primary border-2 border-primary w-full max-w-md">
				<div class="p-6">
					<div class="flex items-center mb-4">
						<x-icons.delete class="w-6 h-6 text-red-500 mr-3" />
						<h3 class="text-lg font-bold text-primary">Delete Task</h3>
					</div>
					<p class="text-primary mb-6">
						Are you sure you want to delete "{{ $task->title }}"? This action cannot be undone.
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