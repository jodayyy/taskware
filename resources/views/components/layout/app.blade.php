@props([
    'title' => 'Taskware',
    'user' => null,
    'page' => '',
    'showHome' => false,
    'guestId' => null
])

<x-layout.base :title="$title" body-class="bg-white min-h-screen relative">
    <x-navigation.topbar :user="$user"/>
    <x-navigation.sidebar/>
    
    @if($page)
        <x-layout.page-breadcrumb :page="$page" :show-home="$showHome" />
    @endif
    
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-2 sm:px-6 lg:px-8">
        <div class="px-4 py-2 sm:px-0">
            {{ $slot }}
        </div>
    </div>
    
    @if(session('is_guest') && $guestId)
        <x-slot name="scripts">
            <script>
                // Store guest ID in localStorage for persistence
                localStorage.setItem('taskware_guest_id', '{{ $guestId }}');
            </script>
        </x-slot>
    @endif
</x-layout.base>