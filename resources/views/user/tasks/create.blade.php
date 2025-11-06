@php
	$isGuest = session('is_guest', false);
	$indexRoute = $isGuest ? route('guest.tasks.index') : route('tasks.index');
	$storeRoute = $isGuest ? route('guest.tasks.store') : route('tasks.store');
	$breadcrumbs = [
		['title' => 'Tasks', 'url' => $indexRoute],
		['title' => 'Create Task', 'url' => null]
	];
@endphp

<x-layout.app 
	title="Create Task - Taskware" 
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
			<div class="p-3 border-b-2 border-primary">
				<h1 class="text-2xl font-bold text-primary">Create New Task</h1>
			</div>

			<!-- Task Form Section -->
			<div class="p-3">
				<form method="POST" action="{{ $storeRoute }}" class="space-y-2">
					@csrf

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
							value="{{ old('title') }}"
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
						>{{ old('description') }}</textarea>
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
									:value="old('deadline')"
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
									:value="old('priority')"
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

							<!-- Project -->
							<div>
								<label for="project_id" class="block text-sm font-medium text-primary mb-2">
									Project (Optional)
								</label>
								@php
									$projectOptions = ['' => 'No Project'];
									if (isset($projects) && $projects->isNotEmpty()) {
										foreach ($projects as $project) {
											$projectOptions[(string)$project->id] = $project->title;
										}
									}
									$selectedProjectId = old('project_id', '');
								@endphp
								<x-form.custom-select 
									name="project_id"
									id="project_id"
									:value="$selectedProjectId"
									:options="$projectOptions"
									placeholder="Select Project"
								/>
								@error('project_id')
									<div class="text-red-500 text-sm mt-1">{{ $message }}</div>
								@enderror
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
						>{{ old('notes') }}</textarea>
						@error('notes')
							<div class="text-red-500 text-sm mt-1">{{ $message }}</div>
						@enderror
					</div>

					<!-- Form Actions -->
					<div class="flex justify-end space-x-3 pt-4 border-primary">
						<a 
							href="{{ $indexRoute }}" 
							class="border-2 border-primary px-4 py-2 text-primary hover:bg-secondary hover:text-secondary"
						>
							Cancel
						</a>

						<button 
							type="submit"
							class="border-2 border-primary px-4 py-2 text-primary hover:bg-secondary hover:text-secondary font-medium"
						>
							Create Task
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-layout.app>

