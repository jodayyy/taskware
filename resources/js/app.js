// Import date picker functionality
import './datepicker.js';

// Theme Toggle Functionality
window.toggleTheme = function() {
  const body = document.body;
  const isDark = body.classList.toggle('dark');
  
  // Store preference in localStorage
  localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
  
  // Update toggle switch
  updateToggleSwitch(isDark);
}

window.setLightMode = function() {
  document.body.classList.remove('dark');
  localStorage.setItem('darkMode', 'disabled');
  updateToggleSwitch(false);
}

window.setDarkMode = function() {
  document.body.classList.add('dark');
  localStorage.setItem('darkMode', 'enabled');
  updateToggleSwitch(true);
}

// Legacy function for backward compatibility
window.toggleDarkMode = function() {
  toggleTheme();
}

window.updateToggleSwitch = function(isDark) {
  const toggleCircle = document.getElementById('toggleCircle');
  const toggleIcon = document.getElementById('toggleIcon');
  
  if (toggleCircle && toggleIcon) {
    if (isDark) {
      // Dark mode - slide right and show moon
      toggleCircle.style.transform = 'translateX(32px)';
      toggleIcon.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>';
    } else {
      // Light mode - slide left and show sun
      toggleCircle.style.transform = 'translateX(0px)';
      toggleIcon.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>';
    }
  }
}

// Initialize the toggle switch immediately when the script loads
window.initializeToggleSwitch = function() {
  const darkMode = localStorage.getItem('darkMode');
  const isDark = darkMode === 'enabled';
  
  if (isDark) {
    document.body.classList.add('dark');
  }
  
  updateToggleSwitch(isDark);
}

// Legacy functions for backward compatibility
window.updateToggleButtons = function(isDark) {
  updateToggleSwitch(isDark);
}

window.updateDarkModeIcon = function(isDark) {
  updateToggleSwitch(isDark);
}

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', function() {
  window.initializeToggleSwitch();
});

// Also try to initialize immediately in case DOM is already ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', window.initializeToggleSwitch);
} else {
  window.initializeToggleSwitch();
}
