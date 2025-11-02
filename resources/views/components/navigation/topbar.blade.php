@props(['user' => null])

<!-- Navigation -->
<nav class="border-b-2 border-black sticky top-0 bg-white z-30">
  <div class="width-full">
    <div class="flex justify-between h-14 px-4">
      <div class="flex items-center">
        @if(session('is_guest'))
          <a href="{{ route('guest.dashboard') }}" class="text-xl font-bold text-black hover:text-gray-700">Taskware</a>
        @else
          <a href="{{ route('dashboard') }}" class="text-xl font-bold text-black hover:text-gray-700">Taskware</a>
        @endif
      </div>
      
      <div class="flex items-center space-x-2">
        @if(session('is_guest'))
          <span class="text-black">{{ $user->username ?? 'Guest' }}</span>
          <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded">Guest</span>
        @else
          <span class="text-black">{{ Auth::user()->username }}</span>
        @endif
        
        <!-- Settings Button -->
        <button onclick="toggleSidebar()" class="rounded text-black p-1 text-sm hover:bg-gray-500 hover:text-white">
          <x-icons.settings />
        </button>

        @if(session('is_guest'))
          <form method="POST" action="{{ route('guest.logout') }}" class="inline">
            @csrf
            <button type="submit" class="rounded text-red-500 p-1 text-sm hover:bg-red-500 hover:text-white">
              <x-icons.logout />
            </button>
          </form>
        @else
          <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="rounded text-red-500 p-1 text-sm hover:bg-red-500 hover:text-white">
              <x-icons.logout />
            </button>
          </form>
        @endif
      </div>
    </div>
  </div>
</nav>