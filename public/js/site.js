(function () {
  var menu = document.getElementById('mobile-menu');
  if (!menu) return;

  function open()  { menu.classList.add('is-open');    document.body.classList.add('is-locked'); }
  function close() { menu.classList.remove('is-open'); document.body.classList.remove('is-locked'); }

  document.querySelectorAll('[data-menu-open]').forEach(function (el) { el.addEventListener('click', open); });
  document.querySelectorAll('[data-menu-close]').forEach(function (el) { el.addEventListener('click', close); });
  document.querySelectorAll('[data-menu-link]').forEach(function (el) { el.addEventListener('click', close); });

  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
  window.matchMedia('(min-width: 900px)').addEventListener('change', function (e) { if (e.matches) close(); });
})();
