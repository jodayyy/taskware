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
	
	<div class="space-y-6">
		<!-- Header with Add Project Button -->
		<div class="flex justify-between items-center">
			<div>
				<h1 class="text-2xl font-bold text-primary">All Projects</h1>
				<p class="text-primary mt-1">Manage and organize your projects</p>
			</div>
			<a 
				href="{{ $createRoute }}"
				class="border-2 border-primary px-4 py-2 text-primary hover:bg-secondary hover:text-secondary flex items-center space-x-2"
			>
				<x-icons.plus class="w-5 h-5" />
				<span>Create Project</span>
			</a>
		</div>

		<!-- Projects List -->
		<div class="border-2 border-primary">
			<div class="p-4 border-b-2 border-primary">
				<h2 class="text-lg font-medium text-primary flex items-center space-x-2">
					<x-icons.project class="w-5 h-5" />
					<span>Projects ({{ $projects->count() }})</span>
				</h2>
			</div>

			<div class="p-4">
				@if($projects->count() > 0)
					<!-- Project List -->
					<div class="space-y-3">
						@foreach($projects as $project)
							<div class="border border-primary p-2 hover:bg-gray-300 hover:bg-opacity-50 text-primary hover:text-primary cursor-pointer transition-colors"
								 onclick="location.href='{{ $isGuest ? route('guest.projects.show', $project->id) : route('projects.show', $project) }}'">
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
                    <div class="mt-6">
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
</x-layout.app>

