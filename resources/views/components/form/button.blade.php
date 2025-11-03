@props([
	'type' => 'submit',
	'class' => 'w-full border-2 border-primary text-primary py-2 px-4 hover:bg-secondary hover:text-secondary',
	'disabled' => false
])

<button 
	type="{{ $type }}"
	@if($disabled) disabled @endif
	{{ $attributes->merge(['class' => $class]) }}
>
	{{ $slot }}
</button>