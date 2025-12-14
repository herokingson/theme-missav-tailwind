document.addEventListener('DOMContentLoaded', function() {
  const tabs = document.querySelectorAll('.js-tab-btn');
  const contents = document.querySelectorAll('.js-tab-content');

  if (tabs.length === 0) return;

  tabs.forEach(tab => {
    tab.addEventListener('click', function() {
      const targetSelector = this.getAttribute('data-target');

      // 1. Reset all tabs styles
      tabs.forEach(t => {
        t.classList.remove('text-pink-500', 'border-pink-500');
        t.classList.add('text-gray-400', 'border-transparent', 'hover:text-gray-200', 'hover:border-gray-700');
      });

      // 2. Activate clicked tab
      this.classList.remove('text-gray-400', 'border-transparent', 'hover:text-gray-200', 'hover:border-gray-700');
      this.classList.add('text-pink-500', 'border-pink-500');

      // 3. Hide all contents
      contents.forEach(c => c.classList.add('hidden'));

      // 4. Show target content
      const targetContent = document.querySelector(targetSelector);
      if (targetContent) {
        targetContent.classList.remove('hidden');
      }
    });
  });
});
