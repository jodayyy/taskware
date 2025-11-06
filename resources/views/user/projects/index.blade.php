@php
	$isGuest = session('is_guest', false);
	$createRoute = $isGuest ? route('guest.projects.create') : route('projects.create');
	$showRoute = $isGuest ? 'guest.projects.show' : 'projects.show';
	$breadcrumbs = [
		['title' => 'Projects', 'url' => null]
	];
@endphp

<x-layout.app 
	title="Projects - Taskware" 
	:user="$user" 
	:breadcrumbs="$breadcrumbs"
	:guest-id="isset($guest_id) ? $guest_id : null"
>
	<!-- Success/Error Messages -->
	<x-form.message type="success" :message="session('success')" />
	<x-form.message type="error" :message="session('error')" />
	
	<div class="space-y-3">
		<!-- Header with Add Project Button -->
		<div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-2 md:space-y-0">
			<div>
				<h1 class="text-2xl font-bold text-primary">All Projects</h1>
				<p class="text-primary mt-1">Manage and organize your projects</p>
			</div>
			<a 
				href="{{ $createRoute }}"
				class="border-2 border-primary px-2 py-2 text-primary hover:bg-secondary hover:text-secondary flex items-center space-x-1"
			>
				<x-icons.plus class="w-5 h-5" />
				<span>Create Project</span>
			</a>
		</div>

		<!-- Projects List -->
		<div class="border-2 border-primary">
			<div class="p-2 border-b-2 border-primary flex flex-col md:flex-row md:justify-between md:items-center space-y-2 md:space-y-0">
				<h2 class="text-lg font-medium text-primary flex items-center space-x-2">
					<x-icons.project class="w-5 h-5" />
					<span>Projects ({{ $projects->count() }})</span>
				</h2>
				@if($projects->count() > 0)
					<div class="flex items-center space-x-2 flex-wrap">
						<!-- Delete Multiple Button (shown by default) -->
						<button 
							type="button"
							id="toggleDeleteModeBtn"
							onclick="toggleDeleteMode()"
							class="border-2 border-primary p-2 text-primary hover:bg-secondary hover:text-secondary text-sm flex items-center space-x-2"
						>
							<x-icons.delete class="w-4 h-4" />
							<span>Delete Multiple</span>
						</button>
						<!-- Selection Controls (hidden by default) -->
						<div id="multipleDeleteControls" class="flex items-center space-x-2 hidden">
							<label class="flex items-center space-x-2 text-primary cursor-pointer">
								<input 
									type="checkbox" 
									id="selectAllProjects" 
									class="w-4 h-4 border-primary text-primary focus:ring-primary"
									onchange="toggleSelectAllProjects(this)"
								>
								<span class="text-sm">Select All</span>
							</label>
							<button 
								type="button"
								id="multipleDeleteProjectsBtn"
								onclick="multipleDeleteProjects()"
								class="border-2 border-red-500 p-2 text-red-500 hover:bg-red-500 hover:text-white text-sm disabled:opacity-50 disabled:cursor-not-allowed"
								disabled
							>
								Delete Selected
							</button>
							<button 
								type="button"
								onclick="cancelDeleteMode()"
								class="border-2 border-primary p-2 text-primary hover:bg-secondary hover:text-secondary text-sm"
							>
								Cancel
							</button>
						</div>
					</div>
				@endif
			</div>

			<div class="px-2 pt-2">
				@if($projects->count() > 0)
					<form id="multipleDeleteProjectsForm" method="POST" action="{{ $isGuest ? route('guest.projects.multiple-destroy') : route('projects.multiple-destroy') }}">
						@csrf
						@method('DELETE')
						<!-- Project List -->
						<div class="space-y-2">
							@foreach($projects as $project)
								<div class="border border-primary hover:bg-gray-300 hover:bg-opacity-50 text-primary hover:text-primary transition-colors">
									<div class="flex-row md:flex justify-between items-start px-2 pb-1.5 pt-1">
										<div class="flex items-start flex-1">
											<input 
												type="checkbox" 
												name="ids[]" 
												value="{{ $project->id }}"
												class="project-checkbox w-4 h-4 border-primary text-primary focus:ring-primary mt-1 mr-2 cursor-pointer hidden"
												onchange="updateMultipleDeleteProjectsButton()"
												onclick="event.stopPropagation()"
											>
											<div 
												class="flex-1 cursor-pointer"
												onclick="location.href='{{ $isGuest ? route('guest.projects.show', $project->id) : route('projects.show', $project) }}'"
											>
												<h3 class="font-medium">{{ $project->title }}</h3>
											</div>
										</div>
										<!-- Right Side: Created Date -->
										<div class="flex items-center space-x-4 text-xs pt-1">
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
					</form>
                    <div class="py-2">
                        {{ $projects->links() }}
                    </div>
				@else
					<!-- Empty State -->
					<div class="text-center py-12">
						<div class="text-primary mb-4">
							<x-icons.project class="mx-auto h-16 w-16" />
						</div>
						<h3 class="text-lg font-medium text-primary mb-2">No projects found</h3>
						<p class="text-primary mb-4">Create your first project to get started!</p>
						<a 
							href="{{ $createRoute }}"
							class="inline-block border-2 border-primary px-6 py-2 text-primary hover:bg-secondary hover:text-secondary"
						>
							Create Your First Project
						</a>
					</div>
				@endif
			</div>
		</div>
	</div>

	<!-- Multiple Delete Confirmation Modal -->
	<div id="multipleDeleteProjectsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
		<div class="flex items-center justify-center min-h-screen p-3">
			<div class="bg-primary border-2 border-primary w-full max-w-md">
				<div class="p-6">
					<div class="flex items-center mb-4">
						<x-icons.delete class="w-6 h-6 text-red-500 mr-3" />
						<h3 class="text-lg font-bold text-primary">Delete Selected Projects</h3>
					</div>
					<p class="text-primary mb-6">
						Are you sure you want to delete <span id="selectedProjectsCount"></span> project(s)? This action cannot be undone. All tasks associated with these projects will also be deleted.
					</p>
					<div class="flex justify-end space-x-3">
						<button 
							onclick="closeMultipleDeleteProjectsModal()"
							class="border-2 border-primary px-4 py-2 text-primary hover:bg-secondary hover:text-secondary"
						>
							Cancel
						</button>
						<button 
							onclick="confirmMultipleDeleteProjects()"
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
			const checkboxes = document.querySelectorAll('.project-checkbox');

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
			const checkboxes = document.querySelectorAll('.project-checkbox');
			const selectAllCheckbox = document.getElementById('selectAllProjects');

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
			const multipleDeleteBtn = document.getElementById('multipleDeleteProjectsBtn');
			if (multipleDeleteBtn) {
				multipleDeleteBtn.disabled = true;
			}
		}

		function toggleSelectAllProjects(checkbox) {
			const checkboxes = document.querySelectorAll('.project-checkbox');
			checkboxes.forEach(cb => {
				cb.checked = checkbox.checked;
			});
			updateMultipleDeleteProjectsButton();
		}

		function updateMultipleDeleteProjectsButton() {
			const checkboxes = document.querySelectorAll('.project-checkbox:checked');
			const multipleDeleteBtn = document.getElementById('multipleDeleteProjectsBtn');
			const selectAllCheckbox = document.getElementById('selectAllProjects');
			
			if (checkboxes.length > 0) {
				multipleDeleteBtn.disabled = false;
			} else {
				multipleDeleteBtn.disabled = true;
			}

			// Update select all checkbox state
			const allCheckboxes = document.querySelectorAll('.project-checkbox');
			if (allCheckboxes.length > 0) {
				selectAllCheckbox.checked = checkboxes.length === allCheckboxes.length;
				selectAllCheckbox.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
			}
		}

		function multipleDeleteProjects() {
			const checkboxes = document.querySelectorAll('.project-checkbox:checked');
			if (checkboxes.length === 0) {
				return;
			}

			document.getElementById('selectedProjectsCount').textContent = checkboxes.length;
			document.getElementById('multipleDeleteProjectsModal').classList.remove('hidden');
		}

		function confirmMultipleDeleteProjects() {
			document.getElementById('multipleDeleteProjectsForm').submit();
		}

		function closeMultipleDeleteProjectsModal() {
			document.getElementById('multipleDeleteProjectsModal').classList.add('hidden');
		}
	</script>
</x-layout.app>

