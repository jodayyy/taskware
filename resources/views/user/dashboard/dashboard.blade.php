<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Dashboard - Taskware</title>
		<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
	</head>
	<body class="bg-white min-h-screen relative">
		<x-navigation.topbar/>
		<x-navigation.sidebar/>
		<x-layout.page-breadcrumb page="Dashboard" :show-home="false" />
		
		<!-- Main Content -->
		<div class="max-w-7xl mx-auto py-2 sm:px-6 lg:px-8">
			<div class="px-4 py-2 sm:px-0">
				<div class="text-center">
					<p class="text-black mb-8">Welcome to your Taskware dashboard!</p>
					
					<!-- Empty state placeholder -->
					<div class="border-2 border-black p-12 max-w-md mx-auto">
						<div class="text-black mb-4">
							<svg class="mx-auto h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
							</svg>
						</div>
						<h3 class="text-lg font-medium text-black mb-2">Your dashboard is ready!</h3>
						<p class="text-black">This is where you'll manage your tasks and projects.</p>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>