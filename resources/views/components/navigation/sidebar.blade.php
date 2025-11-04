<!-- Sidebar Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<div id="sidebar" class="fixed top-0 right-0 h-full w-80 bg-primary border-l-2 border-primary transform translate-x-full transition-transform duration-300 ease-in-out z-50">
  <div class="p-6 flex flex-col h-full">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-lg font-bold text-primary">Menu</h3>
      <button onclick="closeSidebar()" class="text-primary hover:text-gray-600">
        <x-icons.close />
      </button>
    </div>
        
    <!-- Menu Items -->
    <nav class="flex-1 flex flex-col">
      <div class="space-y-2">
        @php
          $currentRoute = request()->route()->getName();
          $isGuest = session('is_guest', false);
          
          // Determine which navigation options to show based on current page
          $isDashboard = in_array($currentRoute, ['dashboard', 'guest.dashboard']);
          $isTasksPage = in_array($currentRoute, ['tasks.index', 'tasks.show', 'guest.tasks.index', 'guest.tasks.task-details']);
          $isProfilePage = in_array($currentRoute, ['profile', 'guest.profile']);
        @endphp
        
        @if($isDashboard)
          {{-- Dashboard page: Show Tasks and Profile Settings --}}
          @if($isGuest)
            <a href="{{ route('guest.tasks.index') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.task />
                <span>Tasks</span>
              </div>
            </a>
            <a href="{{ route('guest.profile') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.profile />
                <span>Profile Settings</span>
              </div>
            </a>
          @else
            <a href="{{ route('tasks.index') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.task />
                <span>Tasks</span>
              </div>
            </a>
            <a href="{{ route('profile') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.profile />
                <span>Profile Settings</span>
              </div>
            </a>
          @endif
        @elseif($isTasksPage)
          {{-- Tasks/Task Details page: Show Dashboard and Profile Settings --}}
          @if($isGuest)
            <a href="{{ route('guest.dashboard') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.home />
                <span>Dashboard</span>
              </div>
            </a>
            <a href="{{ route('guest.profile') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.profile />
                <span>Profile Settings</span>
              </div>
            </a>
          @else
            <a href="{{ route('dashboard') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.home />
                <span>Dashboard</span>
              </div>
            </a>
            <a href="{{ route('profile') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.profile />
                <span>Profile Settings</span>
              </div>
            </a>
          @endif
        @elseif($isProfilePage)
          {{-- Profile Settings page: Show Dashboard and Tasks --}}
          @if($isGuest)
            <a href="{{ route('guest.dashboard') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.home />
                <span>Dashboard</span>
              </div>
            </a>
            <a href="{{ route('guest.tasks.index') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.task />
                <span>Tasks</span>
              </div>
            </a>
          @else
            <a href="{{ route('dashboard') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.home />
                <span>Dashboard</span>
              </div>
            </a>
            <a href="{{ route('tasks.index') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.task />
                <span>Tasks</span>
              </div>
            </a>
          @endif
        @else
          {{-- Default: Show all navigation options --}}
          @if($isGuest)
            <a href="{{ route('guest.dashboard') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.home />
                <span>Dashboard</span>
              </div>
            </a>
            <a href="{{ route('guest.tasks.index') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.task />
                <span>Tasks</span>
              </div>
            </a>
            <a href="{{ route('guest.profile') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.profile />
                <span>Profile Settings</span>
              </div>
            </a>
          @else
            <a href="{{ route('dashboard') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.home />
                <span>Dashboard</span>
              </div>
            </a>
            <a href="{{ route('tasks.index') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.task />
                <span>Tasks</span>
              </div>
            </a>
            <a href="{{ route('profile') }}" 
              class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.profile />
                <span>Profile Settings</span>
              </div>
            </a>
          @endif
        @endif
      </div>
      
      <!-- Fixed Bottom Section -->
      <div class="mt-auto space-y-2">
        <!-- Dark Mode Toggle -->
        <div class="px-4 py-3 border border-primary">
          <div class="flex items-center justify-between">
            <span class="text-primary">Theme</span>
            <!-- Toggle Switch -->
            <div class="relative">
              <button id="themeToggle" onclick="toggleTheme()" 
                class="rounded-full relative w-16 h-8 border-2 border-primary transition-colors duration-300 ease-in-out focus:outline-none">
                <!-- Sliding Circle -->
                <div id="toggleCircle" 
                  class="rounded-full absolute top-0.5 left-0.5 w-6 h-6 bg-primary border border-primary transition-transform duration-300 ease-in-out flex items-center justify-center">
                  <!-- Icon Container -->
                  <div id="toggleIcon" class="text-primary">
                    <x-icons.sun class="w-3 h-3 text-background light-icon hidden" />
                    <x-icons.moon class="w-3 h-3 text-background dark-icon" />
                  </div>
                </div>
              </button>
            </div>
          </div>
        </div>
        
        <!-- Logout Button -->
        @if(session('is_guest'))
          <form method="POST" action="{{ route('guest.logout') }}" class="w-full">
            @csrf
            <button type="submit" class="block w-full text-left px-4 py-3 border border-red-500 text-red-500 hover:bg-red-500 hover:text-white transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.logout />
                <span>Logout</span>
              </div>
            </button>
          </form>
        @else
          <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="block w-full text-left px-4 py-3 border border-red-500 text-red-500 hover:bg-red-500 hover:text-white transition-colors">
              <div class="flex items-center space-x-3">
                <x-icons.logout />
                <span>Logout</span>
              </div>
            </button>
          </form>
        @endif
      </div>
    </nav>
  </div>
</div>

<script>
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebar.classList.contains('translate-x-full')) {
      // Open sidebar
      sidebar.classList.remove('translate-x-full');
      overlay.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
      
      // Initialize toggle switch when sidebar opens
      setTimeout(function() {
        if (window.initializeToggleSwitch) {
          window.initializeToggleSwitch();
        }
      }, 100);
    } else {
      // Close sidebar
      sidebar.classList.add('translate-x-full');
      overlay.classList.add('hidden');
      document.body.style.overflow = 'auto';
    }
  }

  function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.add('translate-x-full');
    overlay.classList.add('hidden');
    document.body.style.overflow = 'auto';
  }

  // Close sidebar with Escape key
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
      closeSidebar();
    }
  });
</script>