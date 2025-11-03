<x-layout.auth title="Login - Taskware" heading="Login to Taskware" subheading="Welcome back!">
    <x-form.message type="success" :message="session('success')" />
    <x-form.errors />
    
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        
        <x-form.input 
            type="text"
            id="username"
            name="username"
            label="Username"
            :value="old('username')"
            required
        />

        <x-form.input 
            type="password"
            id="password"
            name="password"
            label="Password"
            required
        />

        <div class="flex items-center">
            <input 
                type="checkbox" 
                id="remember" 
                name="remember" 
                class="h-4 w-4 border-primary"
            >
            <label for="remember" class="ml-2 block text-sm text-primary">Remember me</label>
        </div>

        <x-form.button type="submit">
            Login
        </x-form.button>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-primary">
            Don't have an account? 
            <a href="{{ route('register') }}" class="underline hover:no-underline">Sign up here</a>
        </p>
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('welcome') }}" class="text-sm text-primary underline hover:no-underline">← Back to Welcome</a>
    </div>
</x-layout.auth>