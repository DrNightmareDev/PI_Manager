document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('[data-seat-pi-manager-root]');
  if (!root) return;
  root.dataset.enhanced = 'true';
});
