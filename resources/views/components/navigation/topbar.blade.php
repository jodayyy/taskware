<!-- Navigation -->
<nav class="border-b-2 border-black sticky top-0 bg-white z-30">
  <div class="width-full">
    <div class="flex justify-between h-14 px-4">
      <div class="flex items-center">
        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-black hover:text-gray-700">Taskware</a>
      </div>
      
      <div class="flex items-center space-x-2">
        <span class="text-black">{{ Auth::user()->username }}</span>
        
        <!-- Settings Button -->
        <button onclick="toggleSidebar()" class="rounded text-black p-1 text-sm hover:bg-gray-500 hover:text-white">
          <x-icons.settings />
        </button>

        <form method="POST" action="{{ route('logout') }}" class="inline">
          @csrf
          <button type="submit" class="rounded text-red-500 p-1 text-sm hover:bg-red-500 hover:text-white">
            <x-icons.logout />
          </button>
        </form>
      </div>
    </div>
  </div>
</nav>