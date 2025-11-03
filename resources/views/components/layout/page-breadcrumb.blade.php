@props(['page' => '', 'showHome' => false])

<div class="px-4 py-2.5">
  <nav class="flex items-center space-x-2 text-sm text-primary">
    @if($showHome)
      @if(session('is_guest'))
        <a href="{{ route('guest.dashboard') }}" class="hover:text-primary transition-colors" title="Home">
          <x-icons.home />
        </a>
      @else
        <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors" title="Home">
          <x-icons.home />
        </a>
      @endif
      <span class="text-primary">/</span>
    @endif
    <span class="text-primary font-medium text-sm">{{ $page }}</span>
  </nav>
</div>