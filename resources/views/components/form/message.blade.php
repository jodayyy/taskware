@props([
    'type' => 'success',
    'message' => ''
])

@php
    $classes = [
        'success' => 'border-green-500 text-green-600 bg-green-50',
        'error' => 'border-red-500 text-red-600 bg-red-50',
        'info' => 'border-blue-500 text-blue-600 bg-blue-50',
        'warning' => 'border-yellow-500 text-yellow-600 bg-yellow-50'
    ][$type] ?? 'border-gray-500 text-gray-600 bg-gray-50';
@endphp

@if($message || session($type) || $slot->isNotEmpty())
    <div class="border {{ $classes }} px-4 py-3 mb-4">
        {{ $message ?: session($type) ?: $slot }}
    </div>
@endif