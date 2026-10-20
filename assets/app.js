/* JJT — skrip dikongsi (pembantu kecil, butang Kembali, data untuk borang) */
(function () {
  const D = window.JJT_DATA || { WA: '', BASE: '/', JENIS: [], DESTINASI: [], PAKEJ: [] };
  const BASE = D.BASE || window.JJT_BASE || '/';
  const BULAN = ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogo', 'Sep', 'Okt', 'Nov', 'Dis'];

  window.JJT = {
    WA: D.WA, WA_PAPAR: D.WA_PAPAR, BASE, JENIS: D.JENIS, DESTINASI: D.DESTINASI, PAKEJ: D.PAKEJ,
    rm: n => 'RM ' + Number(n).toLocaleString('en-MY'),
    tarikhBM: iso => { const [y, m, d] = iso.split('-').map(Number); return d + ' ' + BULAN[m - 1] + ' ' + y; },
    jenis: id => D.JENIS.find(j => j.id === id),
    dest: id => D.DESTINASI.find(d => d.id === id),
    pakej: id => D.PAKEJ.find(p => p.id === id),
    pakejDest: id => D.PAKEJ.filter(p => p.dest === id),
    q: k => new URLSearchParams(location.search).get(k),
    url: p => BASE + String(p || '').replace(/^\//, ''),
    // Hantar lead ke database (dashboard). Pulangkan {ok, kod}; papar amaran jika gagal.
    lead: fd => {
      const url = BASE + 'api/inquiry.php';
      const gagal = kod => {
        const t = document.createElement('div'); t.className = 'lead-err'; t.setAttribute('role', 'alert');
        t.innerHTML = `<b>Maklumat belum tersimpan dalam sistem JJT</b><span>Mesej WhatsApp anda tetap sampai kepada kami. (kod ${esc(String(kod))})</span><button aria-label="Tutup">×</button>`;
        t.querySelector('button').onclick = () => t.remove(); document.body.appendChild(t); setTimeout(() => t.remove(), 12000);
      };
      return fetch(url, { method: 'POST', body: fd, keepalive: true, credentials: 'same-origin', cache: 'no-store' })
        .then(r => r.json().catch(() => ({})).then(j => { const ok = r.ok && j.ok === true; if (!ok) gagal(j.err ? r.status + '-' + j.err : r.status); else jejakHantar(fd); return { ok, kod: r.status }; }))
        .catch(() => { let b = false; try { b = navigator.sendBeacon && navigator.sendBeacon(url, fd); } catch (e) {} if (!b) gagal('rangkaian'); else jejakHantar(fd); return { ok: b, kod: 0 }; });
    }
  };

  /* ---------- Statistik: view page & klik butang (dashboard JJT + GA4 jika dipasang) ---------- */
  const pageKey = () => {
    let p = location.pathname;
    if (p.indexOf(BASE) === 0) p = '/' + p.slice(BASE.length);
    p = p.replace(/\.php$/, '').replace(/\/index$/, '/').replace(/\/+$/, '') || '/';
    const qs = new URLSearchParams(location.search);
    if (p === '/inquiry' && qs.get('p')) p += '?p=' + qs.get('p');
    if (p === '/pakej-detail' && qs.get('p')) p = '/pakej/' + qs.get('p');
    if (p === '/destinasi' && qs.get('d')) p = '/destinasi/' + qs.get('d');
    if (p === '/artikel' && qs.get('a')) p = '/blog/' + qs.get('a');
    return p;
  };
  const jejak = (t, k) => {
    try {
      const d = new URLSearchParams({ t, p: pageKey() });
      if (k) d.set('k', k);
      if (t === 'view') { d.set('r', document.referrer || ''); d.set('u', new URLSearchParams(location.search).get('utm_source') || ''); }
      const url = BASE + 'api/stat.php';
      if (!(navigator.sendBeacon && navigator.sendBeacon(url, d))) fetch(url, { method: 'POST', body: d, keepalive: true, credentials: 'same-origin' });
      if (window.gtag && k) gtag('event', k, { page_path: pageKey() });
    } catch (e) {}
  };
  const jejakHantar = fd => jejak('klik', fd.get('perkara') === 'Itinerari Custom' ? 'hantar_itinerari' : fd.get('jenis') === 'mesej' ? 'hantar_hubungi' : 'hantar_inquiry');
  if (!/[?&](pv|pratonton)=/.test(location.search)) jejak('view');
  document.addEventListener('click', e => {
    const a = e.target.closest && e.target.closest('a[href]');
    if (!a || a.id === 'hantar') return;
    const h = a.getAttribute('href') || '';
    let k = '';
    if (/wa\.me\/|api\.whatsapp\.com/.test(h)) k = 'wa';
    else if (/^tel:/.test(h)) k = 'telefon';
    else if (/^mailto:/.test(h)) k = 'emel';
    else if (/facebook\.com|instagram\.com|tiktok\.com/.test(h)) k = 'sosial';
    else if (/(^|\/)inquiry(\.php)?([?#]|$)/.test(h)) k = 'inquiry';
    else if (/(^|\/)itinerari(\.php)?([?#]|$)/.test(h)) k = 'itinerari';
    else if (/(^|\/)pakej\/[\w-]+|pakej-detail/.test(h)) k = 'lihat_pakej';
    if (k) jejak('klik', k);
  }, true);

  window.ic = (id, cls = '') => `<svg class="ic ${cls}"><use href="#${id}"/></svg>`;
  window.esc = s => String(s).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
  window.waLink = text => `https://wa.me/${D.WA}?text=${encodeURIComponent(text)}`;
  window.durasi = p => p.hari > 0 ? `${p.hari}H ${p.malam}M` : '';   // tempoh belum diisi: kosong
  window.crumb = items => `<nav class="crumb">${items.map((it, i) => i < items.length - 1 ? `<a href="${it[1]}">${it[0]}</a>${ic('chev')}` : `<span>${it[0]}</span>`).join('')}</nav>`;

  // Tutup menu bila pautan ditekan
  document.querySelectorAll('#dr a').forEach(a => a.addEventListener('click', () => document.getElementById('dr').classList.remove('open')));

  // Butang Kembali terapung (semua page kecuali main page)
  if (document.body.dataset.page !== 'utama') {
    document.body.insertAdjacentHTML('beforeend', `<button class="back-fab" id="backFab" aria-label="Kembali ke page sebelum"><svg viewBox="0 0 48 48"><path d="M30 10L16 24l14 14"/></svg><span class="wa-tip">Kembali</span></button>`);
    document.getElementById('backFab').onclick = () => {
      let dariSini = false;
      try { dariSini = document.referrer && new URL(document.referrer).host === location.host; } catch (e) {}
      if (dariSini && history.length > 1) { history.back(); return; }
      const induk = [...document.querySelectorAll('.crumb a')].pop();
      location.href = induk ? induk.href : BASE;
    };
  }
})();
