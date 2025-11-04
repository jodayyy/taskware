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
  const lightIcon = document.querySelector('.light-icon');
  const darkIcon = document.querySelector('.dark-icon');
  
  if (toggleCircle && lightIcon && darkIcon) {
    if (isDark) {
      // Dark mode - slide right and show moon
      toggleCircle.style.transform = 'translateX(32px)';
      lightIcon.classList.add('hidden');
      darkIcon.classList.remove('hidden');
    } else {
      // Light mode - slide left and show sun
      toggleCircle.style.transform = 'translateX(0px)';
      lightIcon.classList.remove('hidden');
      darkIcon.classList.add('hidden');
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
