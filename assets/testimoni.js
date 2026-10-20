/* Halaman Testimoni: tapis kategori, lightbox gambar review, slider video YouTube Shorts (main automatik) */
(function () {
  'use strict';
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  /* ---------- Tapis ikut pakej ---------- */
  $$('#chips .chip').forEach(function (c) {
    c.onclick = function () {
      $$('#chips .chip').forEach(function (x) { x.classList.toggle('on', x === c); });
      $$('#grid .tc').forEach(function (t) { t.style.display = c.dataset.f === 'Semua' || t.dataset.f === c.dataset.f ? '' : 'none'; });
    };
  });

  /* ---------- Lightbox gambar review ---------- */
  var lb = document.getElementById('lb');
  if (lb) {
    var lbImg = lb.querySelector('img'), lbC = lb.querySelector('.lb-c'), senarai = [], ke = 0, asal = null;
    var tunjuk = function (i) {
      ke = (i + senarai.length) % senarai.length;
      lbImg.src = senarai[ke];
      lbC.textContent = senarai.length > 1 ? (ke + 1) + ' / ' + senarai.length : '';
      lb.classList.toggle('satu', senarai.length < 2);
    };
    var buka = function (list, i, dari) {
      senarai = list; asal = dari; tunjuk(i);
      lb.hidden = false; document.documentElement.style.overflow = 'hidden';
      lb.querySelector('.lb-x').focus();
    };
    var tutup = function () {
      lb.hidden = true; lbImg.removeAttribute('src'); document.documentElement.style.overflow = '';
      if (asal) asal.focus();
    };
    $$('.tc-g').forEach(function (g) {
      var list = [];
      try { list = JSON.parse(g.dataset.g); } catch (e) {}
      $$('button[data-i]', g).forEach(function (b) { b.onclick = function () { if (list.length) buka(list, +b.dataset.i, b); }; });
    });
    lb.querySelector('.lb-x').onclick = tutup;
    lb.querySelector('.prev').onclick = function () { tunjuk(ke - 1); };
    lb.querySelector('.next').onclick = function () { tunjuk(ke + 1); };
    lb.addEventListener('click', function (e) { if (e.target === lb) tutup(); });
    document.addEventListener('keydown', function (e) {
      if (lb.hidden) return;
      if (e.key === 'Escape') tutup();
      else if (e.key === 'ArrowLeft') tunjuk(ke - 1);
      else if (e.key === 'ArrowRight') tunjuk(ke + 1);
    });
    var x0 = null;
    lb.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, {passive: true});
    lb.addEventListener('touchend', function (e) {
      if (x0 === null || senarai.length < 2) return;
      var dx = e.changedTouches[0].clientX - x0; x0 = null;
      if (Math.abs(dx) > 40) tunjuk(ke + (dx < 0 ? 1 : -1));
    });
  }

  /* ---------- Slider video Shorts: satu video main pada satu masa (tanpa bunyi), bila habis terus ke video seterusnya ---------- */
  var sl = document.querySelector('.shs');
  if (!sl) return;
  var trk = sl.querySelector('.sld-trk'), it = $$('.shs-it', trk), dots = $$('.sld-dots i', sl);
  var pv = sl.querySelector('.prev'), nx = sl.querySelector('.next');
  var cur = 0, nampak = false, sandaran = null, apiOk = false, gerakKod = 0;
  var hos = 'https://www.youtube-nocookie.com', TT = 'https://www.tiktok.com';

  var src = function (e) {
    if (e.dataset.k === 'tt')   // TikTok: dimainkan melalui arahan (mute → play) selepas pemain sedia
      return TT + '/player/v1/' + e.dataset.id + '?autoplay=0&loop=0&rel=0&music_info=0&description=0&timestamp=0&closed_caption=0&native_context_menu=0';
    return hos + '/embed/' + e.dataset.id + '?autoplay=1&mute=1&playsinline=1&rel=0&modestbranding=1&iv_load_policy=3&enablejsapi=1&origin=' + encodeURIComponent(location.origin);
  };
  var ttArah = function (f, type) { try { f.contentWindow.postMessage({type: type, 'x-tiktok-player': true}, TT); } catch (x) {} };
  var henti = function (e) {
    var f = e && e.querySelector('iframe');
    if (f) f.remove();
    if (e) e.classList.remove('main');
  };
  var main = function (e) {
    if (!e || e.querySelector('iframe')) return;
    var f = document.createElement('iframe');
    f.src = src(e);
    f.title = e.dataset.t;
    f.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
    f.allowFullscreen = true;
    f.onload = function () {   // minta YouTube hantar status pemain (untuk tahu bila video habis)
      if (e.dataset.k === 'tt') return;
      try { f.contentWindow.postMessage(JSON.stringify({event: 'listening', id: e.dataset.id, channel: 'widget'}), hos); } catch (x) {}
    };
    e.appendChild(f);
    e.classList.add('main');
    clearTimeout(sandaran); apiOk = false;
    sandaran = setTimeout(function () { if (!apiOk && nampak) pergi(cur + 1, true); }, 60000);   // jika status tidak diterima: tukar setiap 60 saat
  };
  var tengah = function (i) { return it[i].offsetLeft + it[i].offsetWidth / 2 - trk.clientWidth / 2; };
  var segarNav = function () {
    var max = trk.scrollWidth - trk.clientWidth;
    pv.disabled = trk.scrollLeft < 8; nx.disabled = trk.scrollLeft > max - 8;
  };
  var pilih = function (i) {
    if (i !== cur) henti(it[cur]);
    cur = i;
    it.forEach(function (e, j) { e.classList.toggle('aktif', j === i); });
    dots.forEach(function (d, j) { d.classList.toggle('on', j === i); });
    if (nampak) main(it[i]);
  };
  var pergi = function (i, gerak) {
    i = (i + it.length) % it.length;
    pilih(i);
    if (gerak) { gerakKod = Date.now(); trk.scrollTo({left: Math.max(0, tengah(i)), behavior: 'smooth'}); }
  };

  it.forEach(function (e, j) { e.querySelector('.shs-th').onclick = function () { pergi(j, true); }; });
  pv.onclick = function () { pergi(cur - 1, true); };
  nx.onclick = function () { pergi(cur + 1, true); };

  /* bila pengguna slide sendiri, video yang paling hampir ke tengah menjadi aktif */
  var tunda = null;
  trk.addEventListener('scroll', function () {
    segarNav();
    clearTimeout(tunda);
    tunda = setTimeout(function () {
      if (Date.now() - gerakKod < 900) return;
      var max = trk.scrollWidth - trk.clientWidth, j;
      if (trk.scrollLeft < 8) j = 0;
      else if (trk.scrollLeft > max - 8) j = it.length - 1;
      else {
        var c = trk.scrollLeft + trk.clientWidth / 2, beza = Infinity;
        it.forEach(function (e, k) { var d = Math.abs(e.offsetLeft + e.offsetWidth / 2 - c); if (d < beza) { beza = d; j = k; } });
      }
      if (j !== cur) pilih(j);
    }, 160);
  }, {passive: true});

  /* status daripada pemain YouTube / TikTok: 0 = video habis → video seterusnya */
  window.addEventListener('message', function (ev) {
    var tt = ev.origin === TT;
    if (!tt && !/^https:\/\/www\.youtube(-nocookie)?\.com$/.test(ev.origin)) return;
    var f = it[cur].querySelector('iframe');
    if (!f || ev.source !== f.contentWindow) return;
    var d; try { d = typeof ev.data === 'string' ? JSON.parse(ev.data) : ev.data; } catch (x) { return; }
    if (!d) return;
    var st;
    if (tt) {
      if (!d['x-tiktok-player']) return;
      if (d.type === 'onPlayerReady') { ttArah(f, 'mute'); ttArah(f, 'play'); }   // pelayar hanya benarkan main automatik tanpa bunyi
      else if (d.type === 'onStateChange') st = d.value;
      else if (d.type === 'onPlayerError') {
        if (d.value && d.value.errorCode === 3002) { ttArah(f, 'mute'); ttArah(f, 'play'); return; }
        clearTimeout(sandaran); sandaran = setTimeout(function () { if (nampak) pergi(cur + 1, true); }, 4000); return;   // video tidak boleh dimainkan: langkau
      }
    } else st = d.event === 'onStateChange' ? d.info : (d.event === 'infoDelivery' && d.info ? d.info.playerState : undefined);
    apiOk = true; clearTimeout(sandaran);
    if (st === 0 && nampak) pergi(cur + 1, true);
  });

  /* main hanya bila seksyen video kelihatan di skrin; berhenti bila keluar */
  if ('IntersectionObserver' in window) {
    new IntersectionObserver(function (en) {
      nampak = en[0].isIntersecting;
      if (nampak) main(it[cur]); else { henti(it[cur]); clearTimeout(sandaran); }
    }, {threshold: 0.45}).observe(trk);
  } else { nampak = true; main(it[cur]); }
  document.addEventListener('visibilitychange', function () {
    if (document.hidden) { henti(it[cur]); clearTimeout(sandaran); } else if (nampak) main(it[cur]);
  });
  segarNav();
})();
