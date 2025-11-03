@props([
    'type' => 'submit',
    'class' => 'w-full border-2 border-black text-black py-2 px-4 hover:bg-black hover:text-white',
    'disabled' => false
])

<button 
    type="{{ $type }}"
    @if($disabled) disabled @endif
    {{ $attributes->merge(['class' => $class]) }}
>
    {{ $slot }}
</button>