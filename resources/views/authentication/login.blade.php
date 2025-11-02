<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Login - Taskware</title>
		<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
	</head>
	<body class="bg-white min-h-screen flex items-center justify-center px-4">
		<div class="border-2 border-black p-8 w-96">
			<div class="text-center mb-6">
				<h2 class="text-2xl font-bold text-black">Login to Taskware</h2>
				<p class="text-black mt-2">Welcome back!</p>
			</div>

			@if(session('success'))
				<div class="border border-green-500 text-green-600 px-4 py-3 mb-4">
					{{ session('success') }}
				</div>
			@endif

			@if($errors->any())
				<div class="border border-red-500 text-red-600 px-4 py-3 mb-4">
					<ul class="list-disc list-inside">
						@foreach($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif

				<form method="POST" action="{{ route('login') }}" class="space-y-4">
					@csrf
					
					<div>
						<label for="username" class="block text-sm font-medium text-black">Username</label>
						<input 
							type="text" 
							id="username" 
							name="username" 
							value="{{ old('username') }}"
							required 
							class="mt-1 block w-full px-3 py-2 border border-black focus:outline-none"
						>
					</div>

					<div>
						<label for="password" class="block text-sm font-medium text-black">Password</label>
						<input 
							type="password" 
							id="password" 
							name="password" 
							required 
							class="mt-1 block w-full px-3 py-2 border border-black focus:outline-none"
						>
					</div>

					<div class="flex items-center">
						<input 
							type="checkbox" 
							id="remember" 
							name="remember" 
							class="h-4 w-4 border-black"
						>
						<label for="remember" class="ml-2 block text-sm text-black">Remember me</label>
					</div>

					<button type="submit" class="w-full border-2 border-black text-black py-2 px-4 hover:bg-black hover:text-white">
						Login
					</button>
				</form>

				<div class="mt-6 text-center">
					<p class="text-sm text-black">
						Don't have an account? 
						<a href="{{ route('register') }}" class="underline hover:no-underline">Sign up here</a>
					</p>
				</div>

				<div class="mt-4 text-center">
					<a href="{{ route('welcome') }}" class="text-sm text-black underline hover:no-underline">← Back to Welcome</a>
				</div>
		</div>
	</body>
</html>