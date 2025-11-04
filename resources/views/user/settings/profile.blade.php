@php
	$breadcrumbs = [
		['title' => 'Profile Settings', 'url' => null]
	];
@endphp

<x-layout.app 
	title="Profile - Taskware" 
	:user="$user" 
	:breadcrumbs="$breadcrumbs"
	:guest-id="isset($guest_id) ? $guest_id : null"
>
	<div class="max-w-4xl mx-auto">
		<div class="mb-8">
			<p class="text-primary">Update your account information and password.</p>
		</div>

		<x-form.message type="success" :message="session('success')" />

		<!-- Profile Form -->
		@if(session('is_guest'))
			<form method="POST" action="{{ route('guest.profile.update') }}" class="space-y-6">
				@csrf
				@method('PUT')

				<!-- Guest Information -->
				<div class="border-2 border-yellow-400 bg-yellow-50 bg-opacity-20 p-6">
					<div class="flex items-center mb-4">
						<x-icons.warning class="w-5 h-5 text-yellow-600 mr-2" />
						<h3 class="text-lg font-medium text-yellow-500">Guest Mode</h3>
					</div>
					<p class="text-sm text-yellow-500">You're currently browsing as a guest. Your data is temporary and will be cleared when you close your browser.</p>
				</div>

				<!-- Username Section -->
				<div class="border-2 border-primary p-6">
					<h3 class="text-lg font-medium text-primary mb-4">Account Information</h3>
						
					<x-form.profile-input 
						id="username"
						name="username"
						label="Username"
						:value="old('username', $user->username)"
						required
					/>
				</div>
					
				<!-- Action Buttons -->
				<div class="flex justify-between items-center">
					<a 
						href="{{ route('guest.dashboard') }}" 
						class="border-2 border-primary px-6 py-2 text-primary hover:bg-secondary hover:text-secondary"
					>
						Cancel
					</a>
								
					<button 
						type="submit" 
						class="border-2 border-primary px-6 py-2 text-primary hover:bg-secondary hover:text-secondary"
					>
						Save
					</button>
				</div>
			</form>
		@else
			<form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
				@csrf
				@method('PUT')

				<!-- Username Section -->
				<div class="border-2 border-primary p-6">
					<h3 class="text-lg font-medium text-primary mb-4">Account Information</h3>
						
					<x-form.profile-input 
						id="username"
						name="username"
						label="Username"
						:value="old('username', $user->username)"
						required
					/>
				</div>

				<!-- Password Section -->
				<div class="border-2 border-primary p-6">
					<h3 class="text-lg font-medium text-primary mb-4">Change Password</h3>
					<p class="text-sm text-primary mb-4">Leave password fields empty if you don't want to change your password.</p>
							
					<div class="space-y-4">
						<x-form.profile-input 
							type="password"
							id="current_password"
							name="current_password"
							label="Current Password"
						/>

						<x-form.profile-input 
							type="password"
							id="new_password"
							name="new_password"
							label="New Password"
						/>

						<x-form.profile-input 
							type="password"
							id="new_password_confirmation"
							name="new_password_confirmation"
							label="Confirm New Password"
						/>
					</div>
				</div>
					
				<!-- Action Buttons -->
				<div class="flex justify-between items-center">
					<a 
						href="{{ route('dashboard') }}" 
						class="border-2 border-primary px-6 py-2 text-primary hover:bg-secondary hover:text-secondary"
					>
						Cancel
					</a>
								
					<button 
						type="submit" 
						class="border-2 border-primary px-6 py-2 text-primary hover:bg-secondary hover:text-secondary"
					>
						Save
					</button>
				</div>
			</form>
		@endif
	</div>
</x-layout.app>