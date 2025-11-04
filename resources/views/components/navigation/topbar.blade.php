@props(['user' => null])

<!-- Navigation -->
<nav class="border-b-2 border-primary sticky top-0 bg-primary z-30">
  <div class="width-full">
    <div class="flex justify-between h-14 px-4">
      <div class="flex items-center">
        @if(session('is_guest'))
          <a href="{{ route('guest.dashboard') }}" class="text-xl font-bold text-primary hover:text-gray-700">Taskware</a>
        @else
          <a href="{{ route('dashboard') }}" class="text-xl font-bold text-primary hover:text-gray-700">Taskware</a>
        @endif
      </div>
      
      <div class="flex items-center space-x-4">
        @if(session('is_guest'))
          <span class="text-primary">{{ $user->username ?? 'Guest' }}</span>
          <span class="text-xs border-2 border-primary bg-primary text-primary px-2 py-1 rounded">Guest</span>
        @else
          <span class="text-primary">{{ Auth::user()->username }}</span>
        @endif
        
        <!-- Menu Button -->
        <button onclick="toggleSidebar()" class="rounded text-primary p-1 text-sm hover:bg-secondary hover:text-secondary">
          <x-icons.menu />
        </button>
      </div>
    </div>
  </div>
</nav>