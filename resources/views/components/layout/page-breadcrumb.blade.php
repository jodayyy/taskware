@props(['breadcrumbs' => []])

<div class="px-4 py-2.5">
  <nav class="flex items-center space-x-2 text-sm text-primary">
    @if(count($breadcrumbs) > 0)
      {{-- Home Icon --}}
      @if(session('is_guest'))
        <a href="{{ route('guest.dashboard') }}" class="hover:text-primary transition-colors" title="Home">
          <x-icons.home />
        </a>
      @else
        <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors" title="Home">
          <x-icons.home />
        </a>
      @endif
      
      {{-- Breadcrumb Items --}}
      @foreach($breadcrumbs as $index => $breadcrumb)
        <span class="text-primary">/</span>
        @if(isset($breadcrumb['url']) && $breadcrumb['url'] && $index < count($breadcrumbs) - 1)
          <a href="{{ $breadcrumb['url'] }}" class="hover:text-primary transition-colors">
            {{ $breadcrumb['title'] }}
          </a>
        @else
          <span class="text-primary font-medium">{{ $breadcrumb['title'] }}</span>
        @endif
      @endforeach
    @endif
  </nav>
</div>