(function () {
  // Tutup toast
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-dismiss]');
    if (btn) btn.parentElement.remove();
  });

  // Modal konfirmasi hapus: form dengan atribut data-modal-confirm
  var modal = document.querySelector('[data-modal]');
  if (modal) {
    var pending = null;
    var q = function (s) { return modal.querySelector(s); };

    var closeModal = function () {
      modal.hidden = true;
      document.body.classList.remove('is-locked');
      pending = null;
    };

    document.addEventListener('submit', function (e) {
      var f = e.target;
      if (!f.hasAttribute('data-modal-confirm')) return;
      e.preventDefault();
      pending = f;

      q('[data-modal-title]').textContent    = f.dataset.title  || 'Konfirmasi hapus';
      q('[data-modal-desc]').textContent     = f.dataset.desc   || '';
      q('[data-modal-name]').textContent     = f.dataset.name   || '';
      q('[data-modal-detail]').textContent   = f.dataset.detail || '';
      q('[data-modal-note]').textContent     = f.dataset.note   || '';
      q('[data-modal-ok-label]').textContent = f.dataset.ok     || 'Ya, hapus';

      modal.hidden = false;
      document.body.classList.add('is-locked');
      q('[data-modal-ok]').focus();
    });

    modal.addEventListener('click', function (e) {
      if (e.target.closest('[data-modal-close]')) closeModal();
    });

    q('[data-modal-ok]').addEventListener('click', function () {
      var f = pending;
      closeModal();
      if (f) f.submit();
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !modal.hidden) closeModal();
    });
  }

  // Tutup modal halaman (tambah/edit layanan) dengan Esc
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    var link = document.querySelector('[data-modal-esc]');
    if (link) link.click();
  });

  // Pencarian layanan (filter langsung di browser)
  var search = document.querySelector('[data-service-search]');
  if (search) {
    var rows    = [].slice.call(document.querySelectorAll('[data-service-row]'));
    var counter = document.querySelector('[data-service-count]');

    search.addEventListener('input', function () {
      var term = search.value.trim().toLowerCase();
      var shown = 0, names = {};

      rows.forEach(function (r) {
        var hit = r.dataset.name.indexOf(term) !== -1;
        r.hidden = !hit;
        if (hit) { shown++; names[r.dataset.name] = true; }
      });

      counter.textContent = shown + ' entri / ' + Object.keys(names).length + ' jenis layanan';
    });
  }

  var form = document.querySelector('[data-transaksi-form]');
  if (!form) return;

  var dd       = form.querySelector('[data-dropdown]');
  var hidden   = dd.querySelector('input[type=hidden]');
  var trigger  = dd.querySelector('.dropdown__trigger');
  var labelEl  = dd.querySelector('[data-dropdown-label]');
  var list     = dd.querySelector('.dropdown__list');
  var options  = [].slice.call(list.querySelectorAll('.dropdown__option'));
  var tanggal  = form.querySelector('#tanggal');
  var harga    = form.querySelector('#harga');
  var qty      = form.querySelector('#qty');
  var calc     = form.querySelector('[data-total-calc]');
  var totalEl  = form.querySelector('[data-total-value]');

  function digits(s) { return parseInt(String(s || '').replace(/\D/g, ''), 10) || 0; }
  function fmt(n)    { return Number(n || 0).toLocaleString('id-ID'); }
  function rp(n)     { return 'Rp' + fmt(n); }

  function update() {
    var h = digits(harga.value), q = digits(qty.value);
    calc.textContent    = rp(h) + ' × ' + q;
    totalEl.textContent = rp(h * q);
  }

  function setOpen(open) {
    list.hidden = !open;
    dd.classList.toggle('is-open', open);
    trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  function choose(opt, fillPrice) {
    options.forEach(function (o) {
      o.classList.toggle('is-selected', o === opt);
      o.setAttribute('aria-selected', o === opt ? 'true' : 'false');
    });
    hidden.value = opt ? opt.dataset.value : '';
    labelEl.textContent = opt ? opt.textContent.trim() : 'Pilih layanan';
    if (opt && fillPrice) harga.value = fmt(opt.dataset.harga);
    update();
  }

  trigger.addEventListener('click', function () { setOpen(list.hidden); });
  options.forEach(function (opt) {
    opt.addEventListener('click', function () { choose(opt, true); setOpen(false); });
  });
  document.addEventListener('click', function (e) { if (!dd.contains(e.target)) setOpen(false); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });

  harga.addEventListener('input', function () {
    var d = digits(harga.value);
    harga.value = d ? fmt(d) : '';
    update();
  });
  qty.addEventListener('input', function () {
    qty.value = qty.value.replace(/\D/g, '');
    update();
  });

  var reset = form.querySelector('[data-form-reset]');
  if (reset) {
    reset.addEventListener('click', function () {
      tanggal.value = tanggal.defaultValue;
      qty.value = '1';
      choose(options[0] || null, true);
    });
  }

  // Nilai awal
  var current = options.filter(function (o) { return o.dataset.value === hidden.value; })[0];
  if (current) choose(current, false);
  else if (options.length) choose(options[0], !harga.value);
  else update();
})();