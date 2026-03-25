document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('[data-seat-pi-manager-root]');
  if (root) root.dataset.enhanced = 'true';

  // Sync pill-toggle visual state with its checkbox on click (no page reload needed)
  document.querySelectorAll('.pi-pill-toggle').forEach(label => {
    const cb = label.querySelector('input[type="checkbox"]');
    if (!cb) return;
    cb.addEventListener('change', () => {
      label.classList.toggle('is-active', cb.checked);
    });
  });
});
