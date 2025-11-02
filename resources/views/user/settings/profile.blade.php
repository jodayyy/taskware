<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Profile - Taskware</title>
		<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
	</head>
	<body class="bg-white min-h-screen relative">
		<x-navigation.topbar/>
		<x-navigation.sidebar/>
		<x-layout.page-breadcrumb page="Profile Settings" :show-home="true" />

		<!-- Main Content -->
		<div class="max-w-4xl mx-auto py-2 sm:px-6 lg:px-8">
			<div class="px-4 py-2 sm:px-0">
				<div class="mb-8">
					<p class="text-gray-600">Update your account information and password.</p>
				</div>

				<!-- Success Message -->
				@if(session('success'))
					<div class="mb-6 p-4 border border-green-500 bg-green-50 text-green-800">
						{{ session('success') }}
					</div>
				@endif

				<!-- Profile Form -->
				<form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
					@csrf
					@method('PUT')

					<!-- Username Section -->
					<div class="border-2 border-black p-6">
						<h3 class="text-lg font-medium text-black mb-4">Account Information</h3>
						
						<div>
							<label for="username" class="block text-sm font-medium text-black mb-2">
								Username
							</label>
							<input 
								id="username" 
								name="username" 
								type="text" 
								value="{{ old('username', $user->username) }}" 
								required 
								class="w-full border-2 border-black px-3 py-2 focus:outline-none focus:border-gray-500 @error('username') border-red-500 @enderror"
							>
							@error('username')
								<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<!-- Password Section -->
					<div class="border-2 border-black p-6">
						<h3 class="text-lg font-medium text-black mb-4">Change Password</h3>
						<p class="text-sm text-gray-600 mb-4">Leave password fields empty if you don't want to change your password.</p>
							
						<div class="space-y-4">
							<div>
								<label for="current_password" class="block text-sm font-medium text-black mb-2">
									Current Password
								</label>
								<input 
									id="current_password" 
									name="current_password" 
									type="password" 
									class="w-full border-2 border-black px-3 py-2 focus:outline-none focus:border-gray-500 @error('current_password') border-red-500 @enderror"
								>
								@error('current_password')
									<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
								@enderror
							</div>

							<div>
								<label for="new_password" class="block text-sm font-medium text-black mb-2">
									New Password
								</label>
								<input 
									id="new_password" 
									name="new_password" 
									type="password" 
									class="w-full border-2 border-black px-3 py-2 focus:outline-none focus:border-gray-500 @error('new_password') border-red-500 @enderror"
								>
								@error('new_password')
									<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
								@enderror
							</div>

							<div>
								<label for="new_password_confirmation" class="block text-sm font-medium text-black mb-2">
									Confirm New Password
								</label>
								<input 
									id="new_password_confirmation" 
									name="new_password_confirmation" 
									type="password" 
									class="w-full border-2 border-black px-3 py-2 focus:outline-none focus:border-gray-500"
								>
							</div>
						</div>
					</div>

					<!-- Action Buttons -->
					<div class="flex justify-between items-center">
						<a 
							href="{{ route('dashboard') }}" 
							class="border-2 border-black px-6 py-2 hover:bg-black hover:text-white"
						>
							Cancel
						</a>
							
						<button 
							type="submit" 
							class="border-2 border-black px-6 py-2 hover:bg-black hover:text-white"
						>
							Save
						</button>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>