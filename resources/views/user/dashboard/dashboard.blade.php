<x-layout.app 
    title="Dashboard - Taskware" 
    :user="$user" 
    page="Dashboard" 
    :show-home="false"
    :guest-id="isset($guest_id) ? $guest_id : null"
>
    <div class="text-center">
        <p class="text-primary mb-8">Welcome to your Taskware dashboard!</p>
        
        <!-- Empty state placeholder -->
        <div class="border-2 border-primary p-12 max-w-md mx-auto">
            <div class="text-primary mb-4">
                <svg class="mx-auto h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-primary mb-2">Your dashboard is ready!</h3>
            <p class="text-primary">This is where you'll manage your tasks and projects.</p>
        </div>
    </div>
</x-layout.app>