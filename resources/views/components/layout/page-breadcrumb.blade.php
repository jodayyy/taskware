@props(['page' => '', 'showHome' => false])

<div class="px-4 py-2.5">
  <nav class="flex items-center space-x-2 text-sm text-gray-600">
    @if($showHome)
      @if(session('is_guest'))
        <a href="{{ route('guest.dashboard') }}" class="hover:text-gray-800 transition-colors" title="Home">
          <x-icons.home />
        </a>
      @else
        <a href="{{ route('dashboard') }}" class="hover:text-gray-800 transition-colors" title="Home">
          <x-icons.home />
        </a>
      @endif
      <span class="text-gray-400">/</span>
    @endif
    <span class="text-gray-800 font-medium text-sm">{{ $page }}</span>
  </nav>
</div>