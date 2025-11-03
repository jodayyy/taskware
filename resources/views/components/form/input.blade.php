@props([
    'type' => 'text',
    'id' => '',
    'name' => '',
    'label' => '',
    'value' => '',
    'required' => false,
    'placeholder' => '',
    'helperText' => '',
    'class' => 'mt-1 block w-full px-3 py-2 border border-black focus:outline-none'
])

<div>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-black">{{ $label }}</label>
    @endif
    
    <input 
        type="{{ $type }}"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ $value }}"
        @if($required) required @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        class="{{ $class }} @error($name) border-red-500 @enderror"
    >
    
    @if($helperText)
        <p class="text-xs text-black mt-1">{{ $helperText }}</p>
    @endif
    
    @error($name)
        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
    @enderror
</div>