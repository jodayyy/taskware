@props([
	'task' => null,
	'isEdit' => false
])

<!-- Task Modal Overlay -->
<div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden overflow-y-auto">
	<div class="flex items-start justify-center min-h-screen p-4 py-8">
		<div class="bg-primary border-2 border-primary w-full max-w-md max-h-full overflow-hidden flex flex-col">
			<!-- Modal Header -->
			<div class="flex justify-between items-center p-4 border-b-2 border-primary flex-shrink-0">
				<div class="flex items-center space-x-2">
					@if($isEdit)
						<x-icons.edit class="w-5 h-5 text-primary" />
					@else
						<x-icons.plus class="w-5 h-5 text-primary" />
					@endif
					<h3 class="text-lg font-bold text-primary">
						{{ $isEdit ? 'Edit Task' : 'Create New Task' }}
					</h3>
				</div>
				<button onclick="closeTaskModal()" class="text-primary hover:text-gray-600">
					<x-icons.close class="w-6 h-6" />
				</button>
			</div>

			<!-- Modal Body -->
			<div class="flex-1 overflow-y-auto">
				<form id="taskForm" class="p-6 space-y-2">
					@csrf
					@if($isEdit)
						@method('PUT')
					@endif

					<!-- Title -->
					<div>
						<label for="title" class="block text-sm font-medium text-primary mb-2">
							Title <span class="text-red-500">*</span>
						</label>
						<input 
							type="text" 
							id="title" 
							name="title" 
							required 
							value="{{ old('title', $task->title ?? '') }}"
							class="w-full border-2 border-primary px-3 py-2 bg-primary text-primary focus:outline-none focus:border-gray-500"
							placeholder="Enter task title"
						>
						<div id="titleError" class="text-red-500 text-sm mt-1 hidden"></div>
					</div>

					<!-- Description -->
					<div>
						<label for="description" class="block text-sm font-medium text-primary mb-2">
							Description <span class="text-red-500">*</span>
						</label>
						<textarea 
							id="description" 
							name="description" 
							required 
							rows="3"
							class="w-full border-2 border-primary px-3 py-2 bg-primary text-primary focus:outline-none focus:border-gray-500"
							placeholder="Enter task description"
						>{{ old('description', $task->description ?? '') }}</textarea>
						<div id="descriptionError" class="text-red-500 text-sm mt-1 hidden"></div>
					</div>

					<!-- Deadline -->
					<x-form.date-picker 
						name="deadline" 
						id="deadline"
						label="Deadline" 
						:required="true"
						:value="old('deadline', isset($task) ? $task->deadline->format('d/m/Y') : '')"
					/>
					<div id="deadlineError" class="text-red-500 text-sm mt-1 hidden"></div>

					<!-- Priority -->
					<div>
						<label for="priority" class="block text-sm font-medium text-primary mb-2">
							Priority <span class="text-red-500">*</span>
						</label>
						<x-form.custom-select 
							name="priority"
							id="priority"
							:value="old('priority', $task->priority ?? '')"
							:options="[
								'urgent' => 'Urgent',
								'normal' => 'Normal',
								'low' => 'Low'
							]"
							placeholder="Select Priority"
							required
						/>
						<div id="priorityError" class="text-red-500 text-sm mt-1 hidden"></div>
					</div>

					@if($isEdit)
					<!-- Status (only show in edit mode) -->
					<div>
						<label for="status" class="block text-sm font-medium text-primary mb-2">
							Status <span class="text-red-500">*</span>
						</label>
						<x-form.custom-select 
							name="status"
							id="status"
							:value="old('status', $task->status ?? '')"
							:options="[
								'to_do' => 'To Do',
								'in_progress' => 'In Progress',
								'done' => 'Done'
							]"
							placeholder="Select Status"
							required
						/>
						<div id="statusError" class="text-red-500 text-sm mt-1 hidden"></div>
					</div>
					@endif

					<!-- Notes -->
					<div>
						<label for="notes" class="block text-sm font-medium text-primary mb-2">
							Notes (Optional)
						</label>
						<textarea 
							id="notes" 
							name="notes" 
							rows="2"
							class="w-full border-2 border-primary px-3 py-2 bg-primary text-primary focus:outline-none focus:border-gray-500"
							placeholder="Additional notes"
						>{{ old('notes', $task->notes ?? '') }}</textarea>
						<div id="notesError" class="text-red-500 text-sm mt-1 hidden"></div>
					</div>

					<!-- Modal Footer -->
					<div class="flex justify-end space-x-3 pt-4 border-primary">
						<button 
							type="button" 
							onclick="closeTaskModal()"
							class="border-2 border-primary px-4 py-2 text-primary hover:bg-secondary hover:text-secondary"
						>
							Cancel
						</button>
						<button 
							type="submit" 
							class="border-2 border-primary px-4 py-2 text-primary hover:bg-secondary hover:text-secondary"
						>
							{{ $isEdit ? 'Update' : 'Create' }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	function openTaskModal(taskData = null) {
		try {
			const modal = document.getElementById('taskModal');
			const form = document.getElementById('taskForm');
			
			if (!modal || !form) {
				console.error('Modal or form not found');
				return;
			}
			
			if (taskData) {
				// Edit mode - populate form with task data
				const titleField = document.getElementById('title');
				const descriptionField = document.getElementById('description');
				const notesField = document.getElementById('notes');
				
				if (titleField) titleField.value = taskData.title || '';
				if (descriptionField) descriptionField.value = taskData.description || '';
				if (notesField) notesField.value = taskData.notes || '';
				
				// Handle deadline date conversion
				const deadlineField = document.getElementById('deadline');
				if (deadlineField) {
					if (taskData.deadline) {
						try {
							const deadlineDate = new Date(taskData.deadline);
							if (typeof DatePicker !== 'undefined' && DatePicker.formatDate) {
								deadlineField.value = DatePicker.formatDate(deadlineDate);
							} else {
								// Fallback date formatting
								deadlineField.value = deadlineDate.toLocaleDateString('en-GB');
							}
						} catch (dateError) {
							console.warn('Error formatting deadline date:', dateError);
							deadlineField.value = '';
						}
					} else {
						deadlineField.value = '';
					}
				}
				
				// Handle priority custom select
				try {
					const priorityHidden = document.getElementById('priority') || form.querySelector('input[name="priority"]');
					if (priorityHidden && taskData.priority) {
						priorityHidden.value = taskData.priority;
						
						// Try to update Alpine.js component
						const priorityContainer = priorityHidden.closest('[x-data]');
						if (priorityContainer) {
							// Use Alpine.js $data to access component data
							setTimeout(() => {
								if (window.Alpine && priorityContainer._x_dataStack) {
									const component = priorityContainer._x_dataStack[0];
									if (component) {
										component.selectedValue = taskData.priority;
										if (component.options && component.options[taskData.priority]) {
											component.selectedText = component.options[taskData.priority];
										}
									}
								}
							}, 10);
						}
					}
				} catch (priorityError) {
					console.warn('Error setting priority:', priorityError);
				}
				
				// Handle status custom select
				try {
					const statusHidden = document.getElementById('status') || form.querySelector('input[name="status"]');
					if (statusHidden && taskData.status) {
						statusHidden.value = taskData.status;
						
						// Try to update Alpine.js component
						const statusContainer = statusHidden.closest('[x-data]');
						if (statusContainer) {
							// Use Alpine.js $data to access component data
							setTimeout(() => {
								if (window.Alpine && statusContainer._x_dataStack) {
									const component = statusContainer._x_dataStack[0];
									if (component) {
										component.selectedValue = taskData.status;
										if (component.options && component.options[taskData.status]) {
											component.selectedText = component.options[taskData.status];
										}
									}
								}
							}, 10);
						}
					}
				} catch (statusError) {
					console.warn('Error setting status:', statusError);
				}
				
				// Set form action for editing
				if (window.isGuest) {
					form.action = `/guest/tasks/${taskData.id}`;
				} else {
					form.action = `/tasks/${taskData.id}`;
				}
				
				// Add method spoofing for PUT
				let methodField = form.querySelector('input[name="_method"]');
				if (!methodField) {
					methodField = document.createElement('input');
					methodField.type = 'hidden';
					methodField.name = '_method';
					methodField.value = 'PUT';
					form.appendChild(methodField);
				}
			} else {
				// Create mode - clear form
				form.reset();
				
				// Set form action for creating
				if (window.isGuest) {
					form.action = '/guest/tasks';
				} else {
					form.action = '/tasks';
				}
				
				// Remove method field if exists
				const methodField = form.querySelector('input[name="_method"]');
				if (methodField) {
					methodField.remove();
				}
				
				// Reset custom selects to empty state
				try {
					const priorityHidden = document.getElementById('priority') || form.querySelector('input[name="priority"]');
					if (priorityHidden) {
						priorityHidden.value = '';
						const priorityContainer = priorityHidden.closest('[x-data]');
						if (priorityContainer) {
							setTimeout(() => {
								if (window.Alpine && priorityContainer._x_dataStack) {
									const component = priorityContainer._x_dataStack[0];
									if (component) {
										component.selectedValue = '';
										component.selectedText = '';
									}
								}
							}, 10);
						}
					}
				} catch (priorityResetError) {
					console.warn('Error resetting priority:', priorityResetError);
				}
				
				try {
					const statusHidden = document.getElementById('status') || form.querySelector('input[name="status"]');
					if (statusHidden) {
						statusHidden.value = '';
						const statusContainer = statusHidden.closest('[x-data]');
						if (statusContainer) {
							setTimeout(() => {
								if (window.Alpine && statusContainer._x_dataStack) {
									const component = statusContainer._x_dataStack[0];
									if (component) {
										component.selectedValue = '';
										component.selectedText = '';
									}
								}
							}, 10);
						}
					}
				} catch (statusResetError) {
					console.warn('Error resetting status:', statusResetError);
				}
				
				// Clear deadline field
				const deadlineField = document.getElementById('deadline');
				if (deadlineField) {
					deadlineField.value = '';
				}
			}
			
			// Clear any previous errors
			if (typeof clearFormErrors === 'function') {
				clearFormErrors();
			}
			
			// Show modal
			modal.classList.remove('hidden');
			document.body.style.overflow = 'hidden';
			
		} catch (error) {
			console.error('Error opening task modal:', error);
			alert('An error occurred while opening the task modal. Please refresh the page and try again.');
		}
	}

	function closeTaskModal() {
		const modal = document.getElementById('taskModal');
		modal.classList.add('hidden');
		document.body.style.overflow = 'auto';
		
		// Clear form and errors
		document.getElementById('taskForm').reset();
		clearFormErrors();
	}

	function clearFormErrors() {
		const errorElements = document.querySelectorAll('[id$="Error"]');
		errorElements.forEach(element => {
			element.classList.add('hidden');
			element.textContent = '';
		});
	}

	function showFieldError(fieldName, message) {
		const errorElement = document.getElementById(fieldName + 'Error');
		if (errorElement) {
			errorElement.textContent = message;
			errorElement.classList.remove('hidden');
		}
	}

	// Handle form submission
	document.addEventListener('DOMContentLoaded', function() {
		const taskForm = document.getElementById('taskForm');
		
		taskForm.addEventListener('submit', function(e) {
			e.preventDefault();
			
			const formData = new FormData(this);
			const submitBtn = this.querySelector('button[type="submit"]');
			
			// Client-side validation before submission
			let hasErrors = false;
			
			// Validate title
			const titleInput = document.getElementById('title');
			if (!titleInput.value.trim()) {
				showFieldError('title', 'The title field is required.');
				hasErrors = true;
			}
			
			// Validate description
			const descriptionInput = document.getElementById('description');
			if (!descriptionInput.value.trim()) {
				showFieldError('description', 'The description field is required.');
				hasErrors = true;
			}
			
			// Validate deadline
			const deadlineInput = document.getElementById('deadline');
			let deadlineValue = '';
			if (deadlineInput.value) {
				const parsedDate = DatePicker.parseDate(deadlineInput.value);
				if (parsedDate) {
					deadlineValue = DatePicker.formatDateForServer(parsedDate);
					formData.set('deadline', deadlineValue);
				} else {
					showFieldError('deadline', 'Please enter a valid date.');
					hasErrors = true;
				}
			} else {
				showFieldError('deadline', 'The deadline field is required.');
				hasErrors = true;
			}
			
			// Validate priority
			const priorityInput = document.getElementById('priority');
			if (!priorityInput || !priorityInput.value) {
				showFieldError('priority', 'The priority field is required.');
				hasErrors = true;
			} else {
				// Ensure priority value is in FormData
				formData.set('priority', priorityInput.value);
			}
			
			// Validate status (only for edit mode)
			const methodField = this.querySelector('input[name="_method"]');
			if (methodField && methodField.value === 'PUT') {
				const statusInput = document.getElementById('status');
				if (!statusInput || !statusInput.value) {
					showFieldError('status', 'The status field is required.');
					hasErrors = true;
				} else {
					// Ensure status value is in FormData
					formData.set('status', statusInput.value);
				}
			}
			
			if (hasErrors) {
				submitBtn.disabled = false;
				submitBtn.textContent = methodField && methodField.value === 'PUT' ? 'Update' : 'Create';
				return;
			}
			
			// Disable submit button
			submitBtn.disabled = true;
			submitBtn.textContent = 'Processing...';
			
			// Clear previous errors
			clearFormErrors();
			
			fetch(this.action, {
				method: 'POST',
				body: formData,
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'Accept': 'application/json'
				}
			})
			.then(response => {
				// Check if response is OK (200-299)
				if (response.ok) {
					return response.json();
				}
				
				// Handle validation errors (422) or other errors
				if (response.status === 422) {
					return response.json().then(data => {
						throw { validation: true, errors: data.errors };
					});
				}
				
				// Other errors
				throw new Error(`Server error: ${response.status}`);
			})
			.then(data => {
				if (data.message) {
					// Success - close modal and reload page
					closeTaskModal();
					location.reload();
				}
			})
			.catch(error => {
				console.error('Error:', error);
				
				// Handle validation errors
				if (error.validation && error.errors) {
					Object.keys(error.errors).forEach(field => {
						showFieldError(field, error.errors[field][0]);
					});
				} else {
					// Show generic error
					alert('An error occurred. Please check all required fields and try again.');
				}
			})
			.finally(() => {
				// Re-enable submit button
				submitBtn.disabled = false;
				const isEdit = document.querySelector('input[name="_method"]') !== null;
				submitBtn.textContent = isEdit ? 'Update' : 'Create';
			});
		});
	});

	// Close modal on escape key
	document.addEventListener('keydown', function(event) {
		if (event.key === 'Escape') {
			closeTaskModal();
		}
	});
</script>