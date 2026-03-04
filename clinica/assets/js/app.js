// Sistema Clinica — JS Principal
document.addEventListener('DOMContentLoaded', function() {
  // Sidebar toggle
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  const toggleBtn = document.getElementById('sidebarToggle');
  const closeBtn  = document.getElementById('sidebarClose');

  function openSidebar()  { sidebar?.classList.add('open'); overlay?.classList.add('show'); }
  function closeSidebar() { sidebar?.classList.remove('open'); overlay?.classList.remove('show'); }

  toggleBtn?.addEventListener('click', openSidebar);
  closeBtn?.addEventListener('click', closeSidebar);
  overlay?.addEventListener('click', closeSidebar);

  // Auto-dismiss alerts
  document.querySelectorAll('.alert-auto-dismiss').forEach(el => {
    setTimeout(() => el.classList.add('d-none'), 4000);
  });

  // Confirm delete
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function(e) {
      if (!confirm(this.dataset.confirm || '¿Confirmar esta accion?')) e.preventDefault();
    });
  });

  // Live search (debounced)
  const searchInput = document.getElementById('liveSearch');
  if (searchInput) {
    let timer;
    searchInput.addEventListener('input', function() {
      clearTimeout(timer);
      timer = setTimeout(() => {
        const url = new URL(window.location.href);
        url.searchParams.set('q', this.value);
        window.location.href = url.toString();
      }, 400);
    });
  }
});
