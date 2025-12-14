/**
 * Mobile Menu Toggle
 * Controls the burger menu for mobile devices
 */
document.addEventListener('DOMContentLoaded', function() {
  const burgerToggle = document.getElementById('mt-burger-toggle');
  const mobileMenu = document.getElementById('mt-mobile-menu');
  const mobileBackdrop = document.getElementById('mt-mobile-backdrop');
  const mobilePanel = document.getElementById('mt-mobile-panel');
  const mobileClose = document.getElementById('mt-mobile-close');

  if (!burgerToggle || !mobileMenu) return;

  function openMenu() {
    // Show the menu container
    mobileMenu.style.display = 'block';
    mobileMenu.classList.remove('hidden');

    // Prevent body scrolling
    document.body.style.overflow = 'hidden';

    // Trigger animation after a small delay (for transition to work)
    requestAnimationFrame(() => {
      if (mobileBackdrop) {
        mobileBackdrop.style.opacity = '1';
      }
      if (mobilePanel) {
        mobilePanel.style.transform = 'translateX(0)';
      }
    });
  }

  function closeMenu() {
    // Reverse animation
    if (mobileBackdrop) {
      mobileBackdrop.style.opacity = '0';
    }
    if (mobilePanel) {
      mobilePanel.style.transform = 'translateX(100%)';
    }

    // Re-enable body scrolling
    document.body.style.overflow = '';

    // Hide container after animation completes
    setTimeout(() => {
      mobileMenu.style.display = 'none';
      mobileMenu.classList.add('hidden');
    }, 300);
  }

  // Open menu on burger click
  burgerToggle.addEventListener('click', openMenu);

  // Close menu on close button click
  if (mobileClose) {
    mobileClose.addEventListener('click', closeMenu);
  }

  // Close menu on backdrop click
  if (mobileBackdrop) {
    mobileBackdrop.addEventListener('click', closeMenu);
  }

  // Close menu on Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
      closeMenu();
    }
  });

  // Close menu when clicking on a menu link
  const menuLinks = mobileMenu.querySelectorAll('a');
  menuLinks.forEach(link => {
    link.addEventListener('click', closeMenu);
  });
});

/**
 * Search Bar Toggle (moved here for global availability)
 */
document.addEventListener('DOMContentLoaded', function() {
  const searchToggle = document.getElementById('mt-search-toggle');
  const searchBar = document.getElementById('mt-search-bar');

  if (!searchToggle || !searchBar) return;

  searchToggle.addEventListener('click', function() {
    searchBar.classList.toggle('hidden');

    // Focus the search input when opened
    if (!searchBar.classList.contains('hidden')) {
      const searchInput = searchBar.querySelector('input[name="s"]');
      if (searchInput) {
        searchInput.focus();
      }
    }
  });
});
