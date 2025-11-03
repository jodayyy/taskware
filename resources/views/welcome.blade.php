<x-layout.base title="Welcome to Taskware" body-class="bg-white min-h-screen flex items-center justify-center px-4">
    <div class="text-center">
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-black mb-4">Welcome to Taskware!</h1>
            <p class="text-lg text-black">Your personal task management solution</p>
        </div>
            
        <div class="space-y-2">
            <a 
                href="{{ route('login') }}" 
                class="inline-block border-2 border-black text-black py-3 px-8 hover:bg-black hover:text-white"
            >
                Login / Sign Up
            </a>

            <p class="text-black">or</p>

            <div class="mt-4">
                <button 
                    onclick="startGuestSession()" 
                    class="inline-block border-2 border-black text-black py-3 px-8 hover:bg-black hover:text-white"
                >
                    Continue as Guest
                </button>
            </div>
        </div>
    </div>
    
    <x-slot name="scripts">
        <script>
            function startGuestSession() {
                // Check for existing guest ID in localStorage
                const existingGuestId = localStorage.getItem('taskware_guest_id');
                
                // Create form to submit guest ID if it exists
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = '{{ route('guest.start') }}';
                
                if (existingGuestId) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'existing_guest_id';
                    input.value = existingGuestId;
                    form.appendChild(input);
                }
                
                document.body.appendChild(form);
                form.submit();
            }
        </script>
    </x-slot>
</x-layout.base>