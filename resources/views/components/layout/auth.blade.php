@props([
	'title' => 'Taskware',
	'heading' => '',
	'subheading' => ''
])

<x-layout.base :title="$title" body-class="bg-primary min-h-screen flex items-center justify-center px-4">
	<div class="border-2 border-primary p-8 w-96">
		@if($heading)
			<div class="text-center mb-6">
				<h2 class="text-2xl font-bold text-primary">{{ $heading }}</h2>
				@if($subheading)
					<p class="text-primary mt-2">{{ $subheading }}</p>
				@endif
			</div>
		@endif
		
		{{ $slot }}
	</div>
</x-layout.base>