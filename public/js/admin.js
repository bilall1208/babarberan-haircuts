(function () {
  'use strict';

  function digits(s) { return parseInt(String(s || '').replace(/\D/g, ''), 10) || 0; }
  function fmt(n)    { return Number(n || 0).toLocaleString('id-ID'); }
  function rp(n)     { return 'Rp' + fmt(n); }

  // ---- 1. Tutup toast ----
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-dismiss]');
    if (btn) btn.parentElement.remove();
  });

  // ---- 2. Modal konfirmasi (hapus, logout) ----
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

  // ---- 3. Tutup modal halaman (tambah/edit layanan) dengan Esc ----
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    var link = document.querySelector('[data-modal-esc]');
    if (link) link.click();
  });

  // ---- 4. Pencarian layanan (filter langsung di browser) ----
  var search = document.querySelector('[data-service-search]');
  if (search) {
    var svcRows = [].slice.call(document.querySelectorAll('[data-service-row]'));
    var counter = document.querySelector('[data-service-count]');

    search.addEventListener('input', function () {
      var term = search.value.trim().toLowerCase();
      var shown = 0, names = {};

      svcRows.forEach(function (r) {
        var hit = r.dataset.name.indexOf(term) !== -1;
        r.hidden = !hit;
        if (hit) { shown++; names[r.dataset.name] = true; }
      });

      counter.textContent = shown + ' entri / ' + Object.keys(names).length + ' jenis layanan';
    });
  }

  // ---- 5. Form transaksi: banyak layanan + pembayaran ----
  var form = document.querySelector('[data-transaksi-form]');
  if (!form) return;

  var list     = form.querySelector('[data-items]');
  var tpl      = form.querySelector('#item-template');
  var addBtn   = form.querySelector('[data-item-add]');
  var totalEl  = form.querySelector('[data-total-value]');
  var calcEl   = form.querySelector('[data-total-calc]');
  var tanggal  = form.querySelector('#tanggal');

  var methods    = [].slice.call(form.querySelectorAll('[data-pay-method]'));
  var cashBlocks = [].slice.call(form.querySelectorAll('[data-pay-cash]'));
  var refBlock   = form.querySelector('[data-pay-ref]');
  var bayar      = form.querySelector('#jumlah_bayar');
  var change     = form.querySelector('[data-change]');
  var changeHelp = form.querySelector('[data-change-help]');

  function rows() { return [].slice.call(list.querySelectorAll('[data-item]')); }

  function totals() {
    var total = 0, unit = 0;
    rows().forEach(function (r) {
      total += r._sub || 0;
      unit  += digits(r.querySelector('[data-f="qty"]').value);
    });
    totalEl.textContent = rp(total);
    calcEl.textContent  = rows().length + ' layanan · ' + unit + ' unit';
    renderPay();
  }

  function reindex() {
    var all = rows();
    all.forEach(function (r, i) {
      r.querySelector('[data-item-no]').textContent = 'Layanan ' + (i + 1);
      r.querySelectorAll('[data-f]').forEach(function (el) {
        el.name = 'items[' + i + '][' + el.dataset.f + ']';
      });
      r.querySelector('[data-item-remove]').hidden = all.length < 2;
    });
  }

  function closeDropdowns(except) {
    form.querySelectorAll('.dropdown.is-open').forEach(function (dd) {
      if (except && dd === except) return;
      dd.classList.remove('is-open');
      dd.querySelector('.dropdown__list').hidden = true;
      dd.querySelector('.dropdown__trigger').setAttribute('aria-expanded', 'false');
    });
  }

  function setupRow(row) {
    var dd      = row.querySelector('[data-dropdown]');
    var hidden  = dd.querySelector('input[type=hidden]');
    var trigger = dd.querySelector('.dropdown__trigger');
    var labelEl = dd.querySelector('[data-dropdown-label]');
    var listEl  = dd.querySelector('.dropdown__list');
    var options = [].slice.call(listEl.querySelectorAll('.dropdown__option'));
    var harga   = row.querySelector('[data-f="harga"]');
    var qty     = row.querySelector('[data-f="qty"]');
    var sub     = row.querySelector('[data-subtotal]');

    function update() {
      row._sub = digits(harga.value) * digits(qty.value);
      sub.textContent = rp(row._sub);
      totals();
    }

    function choose(opt, fillPrice) {
      options.forEach(function (o) {
        o.classList.toggle('is-selected', o === opt);
        o.setAttribute('aria-selected', o === opt ? 'true' : 'false');
      });
      hidden.value = opt ? opt.dataset.value : '';
      labelEl.textContent = opt ? opt.textContent.trim() : 'Pilih layanan';
      if (fillPrice) harga.value = opt ? fmt(opt.dataset.harga) : '';
      update();
    }

    trigger.addEventListener('click', function () {
      var open = listEl.hidden;
      closeDropdowns(dd);
      listEl.hidden = !open;
      dd.classList.toggle('is-open', open);
      trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    options.forEach(function (opt) {
      opt.addEventListener('click', function () { choose(opt, true); closeDropdowns(); });
    });

    harga.addEventListener('input', function () {
      var d = digits(harga.value);
      harga.value = d ? fmt(d) : '';
      update();
    });
    qty.addEventListener('input', function () {
      qty.value = qty.value.replace(/\D/g, '');
      update();
    });

    row._choose = choose;

    var current = options.filter(function (o) { return o.dataset.value === hidden.value; })[0];
    if (current) choose(current, false); else update();
  }

  document.addEventListener('click', function (e) {
    if (!e.target.closest('.dropdown')) closeDropdowns();
  });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeDropdowns(); });

  addBtn.addEventListener('click', function () {
    if (rows().length >= 20) return;
    list.insertAdjacentHTML('beforeend', tpl.innerHTML.replace(/__i__/g, rows().length));
    var all = rows();
    var row = all[all.length - 1];
    setupRow(row);
    reindex();
    totals();
    row.querySelector('.dropdown__trigger').focus();
  });

  list.addEventListener('click', function (e) {
    var rm = e.target.closest('[data-item-remove]');
    if (!rm || rows().length < 2) return;
    rm.closest('[data-item]').remove();
    reindex();
    totals();
  });

  // ---- Pembayaran ----
  function metode() {
    var c = methods.filter(function (m) { return m.checked; })[0];
    return c ? c.value : 'tunai';
  }

  function renderPay() {
    var tunai = metode() === 'tunai';
    cashBlocks.forEach(function (el) { el.hidden = !tunai; });
    if (refBlock) refBlock.hidden = tunai;
    if (!tunai) return;

    var total = digits(totalEl.textContent);
    var uang  = digits(bayar.value);

    if (!uang) {
      change.textContent = 'Rp0';
      changeHelp.textContent = '\u00a0';
    } else if (uang >= total) {
      change.textContent = rp(uang - total);
      changeHelp.textContent = '\u00a0';
    } else {
      change.textContent = 'Rp0';
      changeHelp.textContent = 'Kurang ' + rp(total - uang) + ' dari total.';
    }
  }

  methods.forEach(function (m) { m.addEventListener('change', renderPay); });

  bayar.addEventListener('input', function () {
    var d = digits(bayar.value);
    bayar.value = d ? fmt(d) : '';
    renderPay();
  });

  form.querySelectorAll('[data-cash]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var total = digits(totalEl.textContent);
      var nilai = btn.dataset.cash === 'pas' ? total : parseInt(btn.dataset.cash, 10);
      bayar.value = nilai ? fmt(nilai) : '';
      renderPay();
    });
  });

  // ---- Reset formulir ----
  var reset = form.querySelector('[data-form-reset]');
  if (reset) {
    reset.addEventListener('click', function () {
      rows().slice(1).forEach(function (r) { r.remove(); });
      var first = rows()[0];
      first.querySelector('[data-f="qty"]').value = '1';
      first._choose(null, true);

      tanggal.value = tanggal.defaultValue;
      var tunai = form.querySelector('[data-pay-method][value="tunai"]');
      if (tunai) tunai.checked = true;
      bayar.value = '';
      var ref = form.querySelector('#referensi');
      if (ref) ref.value = '';

      reindex();
      totals();
    });
  }

  // ---- Awal ----
  rows().forEach(setupRow);
  reindex();
  totals();
})();