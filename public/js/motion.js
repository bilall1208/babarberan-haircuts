/* motion.js — animasi ringan tanpa library. Aman jika JavaScript mati: semua konten tetap terlihat. */
(function () {
  'use strict';

  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* 1. Elemen muncul halus saat di-scroll (halaman publik) */
  var TEXT = '.about__content, .principle, .approach__eyebrow, .services__intro, .service-item, .walkin__invite, ' +
             '.visit-step, .gallery__head, .gallery__quote, .gallery__disclaimer, .faq__intro, .faq__item, ' +
             '.visit__intro, .visit__item, .closing__eyebrow, .closing__title, .closing__row, .closing__bar';
  var IMG  = '.about__photo, .gallery__item';

  if (!reduce && 'IntersectionObserver' in window) {
    var counts = new Map();

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        var el = en.target;
        io.unobserve(el);
        el.classList.add('is-in');

        // setelah selesai, lepas kelas supaya efek hover elemen itu bekerja normal
        var done = function () {
          el.classList.remove('reveal', 'reveal--img', 'is-in');
          el.style.removeProperty('--rd');
        };
        el.addEventListener('transitionend', function h(e) {
          if (e.target !== el || e.propertyName !== 'transform') return;
          el.removeEventListener('transitionend', h);
          done();
        });
        setTimeout(done, 1600);
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });

    [].slice.call(document.querySelectorAll(TEXT + ',' + IMG)).forEach(function (el) {
      if (el.getBoundingClientRect().top < window.innerHeight * 0.92) return;   // sudah terlihat saat halaman dibuka

      var parent = el.parentElement;
      var i = counts.get(parent) || 0;
      counts.set(parent, i + 1);

      el.style.setProperty('--rd', Math.min(i, 5) * 70 + 'ms');
      el.classList.add('reveal');
      if (el.matches(IMG)) el.classList.add('reveal--img');
      io.observe(el);
    });
  }

  /* 2. Menu navigasi aktif mengikuti bagian yang sedang dilihat */
  var navLinks = [].slice.call(document.querySelectorAll('.pubnav__links a'));
  if (navLinks.length && 'IntersectionObserver' in window) {
    var map = {};
    navLinks.forEach(function (a) {
      var id = a.hash ? a.hash.slice(1) : (a.pathname === location.pathname ? 'home' : null);
      if (id && document.getElementById(id)) map[id] = a;
    });

    var ids = Object.keys(map);
    if (ids.length) {
      var spy = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
          if (!en.isIntersecting) return;
          navLinks.forEach(function (a) { a.classList.remove('is-active'); });
          map[en.target.id].classList.add('is-active');
        });
      }, { rootMargin: '-40% 0px -55% 0px' });
      ids.forEach(function (id) { spy.observe(document.getElementById(id)); });
    }
  }

  /* 3. Angka ringkasan (Dashboard, Laporan) menghitung naik */
  if (!reduce) {
    [].slice.call(document.querySelectorAll('.metric__value')).forEach(function (el) {
      var m = el.textContent.trim().match(/^(\D*?)([\d.]+)(\D*)$/);
      if (!m) return;

      var target = parseInt(m[2].replace(/\./g, ''), 10);
      if (!isFinite(target) || target === 0) return;

      var start = null, dur = 900;
      function step(ts) {
        if (start === null) start = ts;
        var p = Math.min((ts - start) / dur, 1);
        var eased = 1 - Math.pow(1 - p, 3);
        el.textContent = m[1] + Math.round(target * eased).toLocaleString('id-ID') + m[3];
        if (p < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    });
  }

  /* 4. Notifikasi sukses hilang sendiri setelah 5 detik (berhenti saat kursor di atasnya) */
  [].slice.call(document.querySelectorAll('.toast--success')).forEach(function (t) {
    var timer;
    function hide() {
      t.classList.add('is-leaving');
      setTimeout(function () { t.remove(); }, 350);
    }
    timer = setTimeout(hide, 5000);
    t.addEventListener('mouseenter', function () { clearTimeout(timer); });
    t.addEventListener('mouseleave', function () { timer = setTimeout(hide, 2000); });
  });
})();
