@props([
	'title' => 'Taskware',
	'bodyClass' => 'bg-primary min-h-screen'
])

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $title }}</title>
	<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
	@vite(['resources/css/app.css', 'resources/js/app.js'])
	
	{{ $head ?? '' }}
</head>
<body class="{{ $bodyClass }}">
	{{ $slot }}
	
	{{ $scripts ?? '' }}
</body>
</html>