@props([
    'type' => 'text',
    'id' => '',
    'name' => '',
    'label' => '',
    'value' => '',
    'required' => false,
    'class' => 'w-full border-2 border-primary px-3 py-2 bg-primary text-primary focus:outline-none focus:border-gray-500'
])

<div>
  @if($label)
    <label for="{{ $id }}" class="block text-sm font-medium text-primary mb-2">
      {{ $label }}
    </label>
  @endif
    
  <input 
    type="{{ $type }}"
    id="{{ $id }}"
    name="{{ $name }}"
    value="{{ $value }}"
    @if($required) required @endif
    class="{{ $class }} @error($name) border-red-500 @enderror"
  >
    
  @error($name)
    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
  @enderror
</div>