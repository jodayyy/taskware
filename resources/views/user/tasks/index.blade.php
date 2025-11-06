@php
	$isGuest = session('is_guest', false);
	$dashboardRoute = $isGuest ? route('guest.dashboard') : route('dashboard');
@endphp

@php
	$breadcrumbs = [
		['title' => 'Tasks', 'url' => null]
	];
@endphp

<x-layout.app 
	title="Tasks - Taskware" 
	:user="$user" 
	:breadcrumbs="$breadcrumbs"
	:guest-id="isset($guest_id) ? $guest_id : null"
>
	<!-- Success/Error Messages -->
	<x-form.message type="success" :message="session('success')" />
	<x-form.message type="error" :message="session('error')" />
	
	<div class="space-y-6">
		<!-- Header with Add Task Button -->
		<div class="flex justify-between items-center">
			<div>
				<h1 class="text-2xl font-bold text-primary">All Tasks</h1>
				<p class="text-primary mt-1">Manage and organize your tasks</p>
			</div>
			<a 
				href="{{ $isGuest ? route('guest.tasks.create') : route('tasks.create') }}"
				class="border-2 border-primary px-4 py-2 text-primary hover:bg-secondary hover:text-secondary flex items-center space-x-2"
			>
				<x-icons.plus class="w-5 h-5" />
				<span>Create Task</span>
			</a>
		</div>

		<!-- Tasks List -->
		<div class="border-2 border-primary">
			<div class="p-2 border-b-2 border-primary flex justify-between items-center">
				<h2 class="text-lg font-medium text-primary flex items-center space-x-2">
					<x-icons.task class="w-5 h-5" />
					<span>Tasks ({{ $tasks->count() }})</span>
				</h2>
				@if($tasks->count() > 0)
					<div class="flex items-center space-x-2">
						<!-- Delete Multiple Button (shown by default) -->
						<button 
							type="button"
							id="toggleDeleteModeBtn"
							onclick="toggleDeleteMode()"
							class="border-2 border-primary px-4 py-2 text-primary hover:bg-secondary hover:text-secondary text-sm flex items-center space-x-2"
						>
							<x-icons.delete class="w-4 h-4" />
							<span>Delete Multiple</span>
						</button>
						<!-- Selection Controls (hidden by default) -->
						<div id="multipleDeleteControls" class="flex items-center space-x-2 hidden">
							<label class="flex items-center space-x-2 text-primary cursor-pointer">
								<input 
									type="checkbox" 
									id="selectAllTasks" 
									class="w-4 h-4 border-primary text-primary focus:ring-primary"
									onchange="toggleSelectAllTasks(this)"
								>
								<span class="text-sm">Select All</span>
							</label>
							<button 
								type="button"
								id="multipleDeleteTasksBtn"
								onclick="multipleDeleteTasks()"
								class="border-2 border-red-500 px-4 py-2 text-red-500 hover:bg-red-500 hover:text-white text-sm disabled:opacity-50 disabled:cursor-not-allowed"
								disabled
							>
								Delete Selected
							</button>
							<button 
								type="button"
								onclick="cancelDeleteMode()"
								class="border-2 border-primary px-4 py-2 text-primary hover:bg-secondary hover:text-secondary text-sm"
							>
								Cancel
							</button>
						</div>
					</div>
				@endif
			</div>

			<div class="p-2">
				@if($tasks->count() > 0)
					<form id="multipleDeleteTasksForm" method="POST" action="{{ $isGuest ? route('guest.tasks.multiple-destroy') : route('tasks.multiple-destroy') }}">
						@csrf
						@method('DELETE')
						<!-- Task List -->
						<div class="space-y-3">
							@foreach($tasks as $task)
								<div class="border border-primary p-2 hover:bg-gray-300 hover:bg-opacity-50 text-primary hover:text-primary transition-colors">
									<div class="flex-row md:flex justify-between items-start">
										<div class="flex items-start space-x-2 flex-1">
											<input 
												type="checkbox" 
												name="ids[]" 
												value="{{ $task->id }}"
												class="task-checkbox w-4 h-4 border-primary text-primary focus:ring-primary mt-1 cursor-pointer hidden"
												onchange="updateMultipleDeleteButton()"
												onclick="event.stopPropagation()"
											>
											<div 
												class="flex-1 cursor-pointer"
												@if($isGuest)
													onclick="location.href='{{ route('guest.tasks.task-details', $task->id) }}'"
												@else
													onclick="location.href='{{ route('tasks.show', $task) }}'"
												@endif
											>
												<h3 class="font-medium mb-1">{{ $task->title }}</h3>
											</div>
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
					</form>
                    <div class="pt-2">
                        {{ $tasks->links() }}
                    </div>
				@else
					<!-- Empty State -->
					<div class="text-center py-12">
						<div class="text-primary mb-4">
							<x-icons.task class="mx-auto h-16 w-16" />
						</div>
						<h3 class="text-lg font-medium text-primary mb-2">No tasks found</h3>
						<p class="text-primary mb-4">Create your first task to get started!</p>
						<a 
							href="{{ $isGuest ? route('guest.tasks.create') : route('tasks.create') }}"
							class="inline-block border-2 border-primary px-6 py-2 text-primary hover:bg-secondary hover:text-secondary"
						>
							Create Your First Task
						</a>
					</div>
				@endif
			</div>
		</div>
	</div>

	<!-- Multiple Delete Confirmation Modal -->
	<div id="multipleDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
		<div class="flex items-center justify-center min-h-screen p-2">
			<div class="bg-primary border-2 border-primary w-full max-w-md">
				<div class="p-6">
					<div class="flex items-center mb-4">
						<x-icons.delete class="w-6 h-6 text-red-500 mr-3" />
						<h3 class="text-lg font-bold text-primary">Delete Selected Tasks</h3>
					</div>
					<p class="text-primary mb-6">
						Are you sure you want to delete <span id="selectedCount"></span> task(s)? This action cannot be undone.
					</p>
					<div class="flex justify-end space-x-3">
						<button 
							onclick="closeMultipleDeleteModal()"
							class="border-2 border-primary px-4 py-2 text-primary hover:bg-secondary hover:text-secondary"
						>
							Cancel
						</button>
						<button 
							onclick="confirmMultipleDeleteTasks()"
							class="border-2 border-red-500 px-4 py-2 text-red-500 hover:bg-red-500 hover:text-white"
						>
							Delete
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		let deleteModeActive = false;

		function toggleDeleteMode() {
			deleteModeActive = !deleteModeActive;
			const toggleBtn = document.getElementById('toggleDeleteModeBtn');
			const multipleControls = document.getElementById('multipleDeleteControls');
			const checkboxes = document.querySelectorAll('.task-checkbox');

			if (deleteModeActive) {
				// Show selection mode
				toggleBtn.classList.add('hidden');
				multipleControls.classList.remove('hidden');
				checkboxes.forEach(cb => {
					cb.classList.remove('hidden');
				});
			} else {
				// Hide selection mode
				cancelDeleteMode();
			}
		}

		function cancelDeleteMode() {
			deleteModeActive = false;
			const toggleBtn = document.getElementById('toggleDeleteModeBtn');
			const multipleControls = document.getElementById('multipleDeleteControls');
			const checkboxes = document.querySelectorAll('.task-checkbox');
			const selectAllCheckbox = document.getElementById('selectAllTasks');

			// Hide controls
			toggleBtn.classList.remove('hidden');
			multipleControls.classList.add('hidden');
			
			// Hide and uncheck all checkboxes
			checkboxes.forEach(cb => {
				cb.classList.add('hidden');
				cb.checked = false;
			});

			// Reset select all checkbox
			if (selectAllCheckbox) {
				selectAllCheckbox.checked = false;
				selectAllCheckbox.indeterminate = false;
			}

			// Disable delete button
			const multipleDeleteBtn = document.getElementById('multipleDeleteTasksBtn');
			if (multipleDeleteBtn) {
				multipleDeleteBtn.disabled = true;
			}
		}

		function toggleSelectAllTasks(checkbox) {
			const checkboxes = document.querySelectorAll('.task-checkbox');
			checkboxes.forEach(cb => {
				cb.checked = checkbox.checked;
			});
			updateMultipleDeleteButton();
		}

		function updateMultipleDeleteButton() {
			const checkboxes = document.querySelectorAll('.task-checkbox:checked');
			const multipleDeleteBtn = document.getElementById('multipleDeleteTasksBtn');
			const selectAllCheckbox = document.getElementById('selectAllTasks');
			
			if (checkboxes.length > 0) {
				multipleDeleteBtn.disabled = false;
			} else {
				multipleDeleteBtn.disabled = true;
			}

			// Update select all checkbox state
			const allCheckboxes = document.querySelectorAll('.task-checkbox');
			if (allCheckboxes.length > 0) {
				selectAllCheckbox.checked = checkboxes.length === allCheckboxes.length;
				selectAllCheckbox.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
			}
		}

		function multipleDeleteTasks() {
			const checkboxes = document.querySelectorAll('.task-checkbox:checked');
			if (checkboxes.length === 0) {
				return;
			}

			document.getElementById('selectedCount').textContent = checkboxes.length;
			document.getElementById('multipleDeleteModal').classList.remove('hidden');
		}

		function confirmMultipleDeleteTasks() {
			document.getElementById('multipleDeleteTasksForm').submit();
		}

		function closeMultipleDeleteModal() {
			document.getElementById('multipleDeleteModal').classList.add('hidden');
		}
	</script>
</x-layout.app>