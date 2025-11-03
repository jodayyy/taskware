@props([
	'title' => 'Taskware',
	'user' => null,
	'page' => '',
	'showHome' => false,
	'breadcrumbs' => [],
	'guestId' => null
])

<x-layout.base :title="$title" body-class="bg-primary min-h-screen relative">
	<x-navigation.topbar :user="$user"/>
	<x-navigation.sidebar/>
	
	@if($page)
		<x-layout.page-breadcrumb :page="$page" :show-home="$showHome" />
	@elseif(count($breadcrumbs) > 0)
		<x-layout.page-breadcrumb :breadcrumbs="$breadcrumbs" />
	@endif
	
	<!-- Main Content -->
	<div class="max-w-7xl mx-auto py-2 sm:px-6 lg:px-8">
		<div class="px-4 py-2 sm:px-0">
			{{ $slot }}
		</div>
	</div>
	
	<x-slot name="scripts">
		@if(session('is_guest') && $guestId)
			<script>
				// Store guest ID in localStorage for persistence
				localStorage.setItem('taskware_guest_id', '{{ $guestId }}');
			</script>
		@endif
		
		{{ $scripts ?? '' }}
	</x-slot>
</x-layout.base>