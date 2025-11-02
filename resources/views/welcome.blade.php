<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Welcome to Taskware</title>
		<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
	</head>
	<body class="bg-white min-h-screen flex items-center justify-center px-4">
		<div class="text-center">
			<div class="mb-10">
				<h1 class="text-4xl font-bold text-black mb-4">Welcome to Taskware!</h1>
				<p class="text-lg text-black">Your personal task management solution</p>
			</div>
				
			<div class="space-y-4">
				<a 
					href="{{ route('login') }}" 
					class="inline-block border-2 border-black text-black py-3 px-8 hover:bg-black hover:text-white"
				>
					Login / Sign Up
				</a>
			</div>
		</div>
	</body>
</html>