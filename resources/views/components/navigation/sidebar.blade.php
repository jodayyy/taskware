<!-- Sidebar Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<div id="sidebar" class="fixed top-0 right-0 h-full w-80 bg-primary border-l-2 border-primary transform translate-x-full transition-transform duration-300 ease-in-out z-50">
  <div class="p-6 flex flex-col h-full">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-lg font-bold text-primary">Settings</h3>
      <button onclick="closeSidebar()" class="text-primary hover:text-gray-600">
        <x-icons.close />
      </button>
    </div>
        
    <!-- Menu Items -->
    <nav class="flex-1 flex flex-col">
      <div class="space-y-2">
        @if(session('is_guest'))
          <a href="{{ route('guest.profile') }}" 
            class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
            <div class="flex items-center space-x-3">
              <x-icons.profile />
              <span>Profile</span>
            </div>
          </a>
        @else
          <a href="{{ route('profile') }}" 
            class="block w-full text-left px-4 py-3 border border-primary text-primary hover:bg-secondary hover:text-secondary transition-colors">
            <div class="flex items-center space-x-3">
              <x-icons.profile />
              <span>Profile</span>
            </div>
          </a>
        @endif
      </div>
      
      <!-- Dark Mode Toggle - Fixed at bottom -->
      <div class="mt-auto">
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
                  </div>
                </div>
              </button>
            </div>
          </div>
        </div>
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