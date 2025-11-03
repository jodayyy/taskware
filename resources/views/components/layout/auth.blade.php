@props([
    'title' => 'Taskware',
    'heading' => '',
    'subheading' => ''
])

<x-layout.base :title="$title" body-class="bg-white min-h-screen flex items-center justify-center px-4">
    <div class="border-2 border-black p-8 w-96">
        @if($heading)
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-black">{{ $heading }}</h2>
                @if($subheading)
                    <p class="text-black mt-2">{{ $subheading }}</p>
                @endif
            </div>
        @endif
        
        {{ $slot }}
    </div>
</x-layout.base>