<x-layout.auth title="Register - Taskware" heading="Join Taskware" subheading="Create your account">
    <x-form.errors />

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        
        <x-form.input 
            type="text"
            id="username"
            name="username"
            label="Username"
            :value="old('username')"
            required
            helper-text="Choose a unique username"
        />

        <x-form.input 
            type="password"
            id="password"
            name="password"
            label="Password"
            required
            helper-text="Minimum 6 characters"
        />

        <x-form.input 
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            label="Confirm Password"
            required
        />

        <x-form.button type="submit">
            Create Account
        </x-form.button>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-black">
            Already have an account? 
            <a href="{{ route('login') }}" class="underline hover:no-underline">Login here</a>
        </p>
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('welcome') }}" class="text-sm text-black underline hover:no-underline">← Back to Welcome</a>
    </div>
</x-layout.auth>