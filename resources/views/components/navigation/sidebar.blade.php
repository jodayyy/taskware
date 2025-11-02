<!-- Sidebar Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<div id="sidebar" class="fixed top-0 right-0 h-full w-80 bg-white border-l-2 border-black transform translate-x-full transition-transform duration-300 ease-in-out z-50">
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-lg font-bold text-black">Settings</h3>
      <button onclick="closeSidebar()" class="text-black hover:text-gray-600">
        <x-icons.close />
      </button>
    </div>
        
    <!-- Menu Items -->
    <nav class="space-y-2">
      <a href="{{ route('profile') }}" 
        class="block w-full text-left px-4 py-3 border border-black hover:bg-black hover:text-white transition-colors">
        <div class="flex items-center space-x-3">
          <x-icons.profile />
          <span>Profile</span>
        </div>
      </a>
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