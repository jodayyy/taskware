@php
	$isGuest = session('is_guest', false);
	$indexRoute = $isGuest ? route('guest.projects.index') : route('projects.index');
	$storeRoute = $isGuest ? route('guest.projects.store') : route('projects.store');
	$breadcrumbs = [
		['title' => 'Projects', 'url' => $indexRoute],
		['title' => 'Create Project', 'url' => null]
	];
@endphp

<x-layout.app 
	title="Create Project - Taskware" 
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
			<div class="p-4 border-b-2 border-primary">
				<h1 class="text-2xl font-bold text-primary">Create New Project</h1>
			</div>

			<!-- Project Form Section -->
			<div class="p-4">
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
							placeholder="Enter project title"
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
							rows="6"
							class="w-full border-2 border-primary px-4 py-3 bg-primary text-primary focus:outline-none focus:border-gray-500"
							placeholder="Enter project description"
						>{{ old('description') }}</textarea>
						@error('description')
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
							Create Project
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-layout.app>

