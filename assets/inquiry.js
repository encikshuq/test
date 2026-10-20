/* Borang inquiry: pilih pakej, kira anggaran, pratonton mesej WhatsApp & simpan ke database */
(function () {
  const J = JJT;
  const PLANE = document.getElementById('plane-tpl').innerHTML;
  let p = J.pakej(J.q('p'));
  const NEGERI = (window.JJT_DATA && JJT_DATA.NEGERI) || ['Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Perak', 'Perlis', 'Pulau Pinang', 'Sabah', 'Sarawak', 'Selangor', 'Terengganu', 'W.P. Kuala Lumpur', 'W.P. Putrajaya', 'W.P. Labuan', 'Luar negara'];
  const IKB = ['bed', 'users', 'f-group'];
  const BILIK = (window.JJT_DATA && JJT_DATA.BILIK) ? JJT_DATA.BILIK.map((b, i) => [b.id || 'b' + i, b.n, b.k, IKB[i % 3]]) : [['twin', 'Twin / Double', '2 orang sebilik', 'bed'], ['triple', 'Triple', '3 orang sebilik', 'users'], ['single', 'Single', '1 orang sebilik', 'f-group']];
  const pax = { dewasa: 2, kanak: 0, bayi: 0 };
  const PIL = (window.JJT_DATA && JJT_DATA.PILIHAN) || {};
  let pil = PIL[J.q('k')] ? J.q('k') : 'standard';
  const adaPil = () => !!(p && p.jenis !== 'series' && PIL[pil]);
  const pilTxt = () => adaPil() ? `${PIL[pil].nama} (${PIL[pil].ket})` : '';
  const today = new Date().toISOString().slice(0, 10);

  const pkOpts = J.DESTINASI.filter(d => J.pakejDest(d.id).length).map(d =>
    `<optgroup label="${esc(d.nama)}">${J.pakejDest(d.id).map(x => `<option value="${x.id}">${esc(d.pendek || d.nama)}: ${esc(x.nama)} (${durasi(x) ? durasi(x) + ' · ' : ''}${J.jenis(x.jenis).nama})</option>`).join('')}</optgroup>`).join('');

  document.getElementById('app').innerHTML = `
    <section class="pb">
      ${PLANE}
      <div id="crumb"></div>
      <h1>Borang <em>Inquiry</em></h1>
      <p>Isi maklumat ringkas di bawah. Kami akan semak kekosongan dan hantar sebut harga melalui WhatsApp — tiada bayaran untuk bertanya.</p>
    </section>

    <div class="card steps">
      <div class="stp done"><i>${ic('check')}</i>Pilih pakej</div><span class="sline done"></span>
      <div class="stp now"><i>2</i>Isi maklumat</div><span class="sline"></span>
      <div class="stp" id="st3"><i>3</i>Hantar ke WhatsApp</div>
    </div>

    <div class="flay" style="margin-top:calc(16*var(--u))">
      <form class="card fcard" id="f" novalidate><input id="web" name="web" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;opacity:0" aria-hidden="true">
        <div class="fsec">
          <h3><span class="n">1</span>Pakej &amp; Tarikh</h3>
          <div class="grid2">
            <div class="fld full"><label class="lbl" for="pk">Pakej pilihan</label>
              <select class="in" id="pk"><option value="">Belum pasti — minta cadangan daripada JJT</option>${pkOpts}</select></div>
            <div class="fld full" id="tWrap"></div>
            <div class="fld full" id="pilWrap"></div>
          </div>
        </div>

        <div class="fsec">
          <h3><span class="n">2</span>Bilangan Peserta</h3>
          <div class="pax">
            ${[['dewasa', 'Dewasa', '12 tahun ke atas'], ['kanak', 'Kanak-kanak', '2 – 11 tahun'], ['bayi', 'Bayi', 'Bawah 2 tahun']].map(([k, n, s]) => `
              <div><div><b>${n}</b><small>${s}</small></div>
                <div class="stepper"><button type="button" data-k="${k}" data-d="-1" aria-label="Kurang ${n}">${ic('minus')}</button><output id="o-${k}">${pax[k]}</output><button type="button" data-k="${k}" data-d="1" aria-label="Tambah ${n}">${ic('plus')}</button></div></div>`).join('')}
          </div>
          <div class="fld" style="margin-top:calc(10*var(--u))"><span class="lbl">Jenis bilik</span>
            <div class="opts">${BILIK.map(([v, n, s, i], k) => `<label class="opt"><input type="radio" name="bilik" value="${v}" ${k === 0 ? 'checked' : ''}><span>${ic(i)}${n}<small>${s}</small></span></label>`).join('')}</div>
          </div>
        </div>

        <div class="fsec">
          <h3><span class="n">3</span>Maklumat Anda</h3>
          <div class="grid2">
            <div class="fld" id="fNama"><label class="lbl" for="nama">Nama penuh <em>*</em></label><input class="in" id="nama" autocomplete="name" placeholder="Cth: Siti Aminah binti Ahmad"><span class="ferr">Sila isi nama penuh.</span></div>
            <div class="fld" id="fTel"><label class="lbl" for="tel">No. telefon (WhatsApp) <em>*</em></label><input class="in" id="tel" type="tel" inputmode="tel" autocomplete="tel" placeholder="Cth: 012-345 6789"><span class="ferr">Sila isi nombor telefon yang sah.</span></div>
            <div class="fld" id="fEmel"><label class="lbl" for="emel">Emel</label><input class="in" id="emel" type="email" autocomplete="email" placeholder="nama@contoh.com"><span class="ferr">Format emel tidak sah.</span></div>
            <div class="fld"><label class="lbl" for="negeri">Negeri</label><select class="in" id="negeri"><option value="">Pilih negeri</option>${NEGERI.map(n => `<option>${n}</option>`).join('')}</select></div>
            <div class="fld full"><label class="lbl" for="nota">Permintaan khas <span class="muted">(pilihan)</span></label><textarea class="in" id="nota" placeholder="Cth: perlukan kerusi roda, bajet anggaran, hotel dekat masjid…"></textarea></div>
          </div>
        </div>

        <div class="fsec">
          <div class="fld" id="fSetuju"><label class="chk"><input type="checkbox" id="setuju"><span>Saya bersetuju dengan <a href="${J.url('polisi/terma')}" target="_blank">Terma &amp; Syarat</a> dan <a href="${J.url('polisi/privasi')}" target="_blank">Polisi Privasi</a> JJT.</span></label><span class="ferr">Sila tandakan persetujuan.</span></div>
          <button class="btn btn-gr btn-full" style="margin-top:calc(12*var(--u));height:calc(36*var(--u));font-size:calc(10*var(--u))"><svg class="w"><use href="#wa"/></svg>Semak &amp; Hantar ke WhatsApp</button>
          <p class="hint" style="text-align:center;margin-top:calc(6*var(--u))">Anda akan melihat pratonton mesej sebelum ia dihantar.</p>
        </div>
      </form>

      <aside class="card sum" id="sum"></aside>
    </div>`;

  const $ = id => document.getElementById(id);
  const sel = $('pk');
  if (p) sel.value = p.id;

  function renderTarikh() {
    const t0 = J.q('t');
    if (p && Array.isArray(p.tarikh)) {
      $('tWrap').innerHTML = `<label class="lbl" for="tarikh">Tarikh berlepas <em>*</em></label>
        <select class="in" id="tarikh">${p.tarikh.map(t => `<option value="${t.t}" ${t.s === 'penuh' ? 'disabled' : ''} ${t.t === t0 ? 'selected' : ''}>${J.tarikhBM(t.t)} · ${t.s === 'penuh' ? 'Penuh' : t.k + ' tempat lagi'}</option>`).join('')}</select>
        <span class="hint">Series Trip berlepas pada tarikh tetap.</span>`;
      const s = $('tarikh'); if (s.selectedOptions[0] && s.selectedOptions[0].disabled) s.value = (p.tarikh.find(t => t.s !== 'penuh') || {}).t;
    } else {
      $('tWrap').innerHTML = `<label class="lbl" for="tarikh">Tarikh cadangan berlepas</label>
        <input class="in" id="tarikh" type="date" min="${today}">
        <span class="hint">Tarikh fleksibel — biarkan kosong jika belum pasti.</span>`;
    }
    $('tarikh').onchange = renderSum;
    renderPil();
  }

  // Pilihan pakej: Basic / Standard / Premium (bukan untuk Series Trip)
  function renderPil() {
    if (!(p && p.jenis !== 'series' && Object.keys(PIL).length)) { $('pilWrap').innerHTML = ''; $('pilWrap').style.display = 'none'; return; }
    $('pilWrap').style.display = '';
    $('pilWrap').innerHTML = `<span class="lbl">Pilihan pakej</span>
      <div class="opts opts-l">${Object.entries(PIL).map(([k, x]) => `<label class="opt"><input type="radio" name="pil" value="${k}" ${k === pil ? 'checked' : ''}><span>${ic(x.ikon)}${esc(x.nama)}<small>${esc(x.ket)}</small></span></label>`).join('')}</div>`;
    document.querySelectorAll('[name=pil]').forEach(r => r.onchange = () => { pil = r.value; const u = new URL(location); u.searchParams.set('k', pil); history.replaceState(null, '', u); renderSum(); });
  }

  function anggaran() {
    if (!p) return null;
    // Contoh kiraan: kanak-kanak 80%, bayi 10% (tertakluk kepada pakej sebenar)
    return pax.dewasa * p.harga + pax.kanak * Math.round(p.harga * 0.8) + pax.bayi * Math.round(p.harga * 0.1);
  }
  const paxTxt = () => [pax.dewasa && pax.dewasa + ' dewasa', pax.kanak && pax.kanak + ' kanak-kanak', pax.bayi && pax.bayi + ' bayi'].filter(Boolean).join(', ');
  const tarikhTxt = () => { const v = $('tarikh').value; return v ? J.tarikhBM(v) : 'Fleksibel / belum pasti'; };
  const bilikTxt = () => BILIK.find(b => b[0] === document.querySelector('[name=bilik]:checked').value)[1];

  function renderSum() {
    const d = p && J.dest(p.dest), j = p && J.jenis(p.jenis), a = anggaran();
    $('sum').innerHTML = p ? `
      <div class="sum-img${(p.img || d.img) ? '' : ' noimg'}"${(p.img || d.img) ? ` style="background-image:url(${p.img || d.img})"` : ''}><span class="bdg nv">${ic(j.ikon)} ${j.nama}</span></div>
      <h3>${esc(d.pendek || d.nama)}: ${esc(p.nama)}</h3>
      <dl>${durasi(p) ? `<dt>Tempoh</dt><dd>${durasi(p)}</dd>` : ''}${adaPil() ? `<dt>Pilihan</dt><dd>${esc(PIL[pil].nama)}</dd>` : ''}<dt>Tarikh</dt><dd>${tarikhTxt()}</dd><dt>Peserta</dt><dd>${paxTxt()}</dd><dt>Bilik</dt><dd>${bilikTxt()}</dd></dl>
      <div class="tot"><small>Anggaran jumlah</small><b>${J.rm(a)}</b></div>
      <p class="fine">Anggaran sahaja (contoh: kanak-kanak 80%, bayi 10% daripada harga dewasa). Harga muktamad akan disahkan oleh JJT.</p>
      <a class="lnk" href="${J.url('pakej/' + p.id)}" style="display:inline-block;margin-top:calc(8*var(--u))">← Kembali ke detail pakej</a>`
      : `<div class="sum-img noimg"><span class="bdg">${ic('spark')} Cadangan</span></div>
      <h3>Minta cadangan pakej</h3>
      <p class="fine" style="font-size:calc(8.2*var(--u))">Belum pasti nak ke mana? Isi bilangan peserta dan permintaan anda (bajet, tarikh, destinasi idaman). Kami akan cadangkan pakej yang sesuai.</p>
      <dl><dt>Tarikh</dt><dd>${tarikhTxt()}</dd><dt>Peserta</dt><dd>${paxTxt()}</dd><dt>Bilik</dt><dd>${bilikTxt()}</dd></dl>
      <a class="btn btn-ol btn-full" style="margin-top:calc(10*var(--u))" href="${J.url('pakej')}">Lihat semua pakej</a>`;
    const d2 = p && J.dest(p.dest);
    $('crumb').innerHTML = crumb(p ? [['Utama', J.url('')], ['Pakej', J.url('pakej')], [esc(d2.pendek || d2.nama), J.url('destinasi/' + d2.id)], [esc(p.nama), J.url('pakej/' + p.id)], ['Inquiry']] : [['Utama', J.url('')], ['Inquiry']]);
  }

  sel.onchange = () => { p = J.pakej(sel.value); renderTarikh(); renderSum(); const u = new URL(location); p ? u.searchParams.set('p', p.id) : u.searchParams.delete('p'); u.searchParams.delete('t'); history.replaceState(null, '', u); };
  document.querySelectorAll('.stepper button').forEach(b => b.onclick = () => {
    const k = b.dataset.k; pax[k] = Math.max(k === 'dewasa' ? 1 : 0, Math.min(20, pax[k] + +b.dataset.d));
    $('o-' + k).textContent = pax[k]; renderSum();
  });
  document.querySelectorAll('[name=bilik]').forEach(r => r.onchange = renderSum);
  renderTarikh(); renderSum();

  // Validasi
  function check(id, ok) { $(id).classList.toggle('bad', !ok); const i = $(id).querySelector('.in'); if (i) i.classList.toggle('err', !ok); return ok; }
  function valid() {
    const a = check('fNama', $('nama').value.trim().length >= 3);
    const b = check('fTel', $('tel').value.replace(/\D/g, '').length >= 9);
    const e = $('emel').value.trim(); const c = check('fEmel', !e || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e));
    const s = check('fSetuju', $('setuju').checked);
    return a && b && c && s;
  }
  ['nama', 'tel', 'emel'].forEach(id => $(id).addEventListener('input', () => $(id).closest('.fld').classList.contains('bad') && valid()));
  $('setuju').onchange = () => $('fSetuju').classList.contains('bad') && valid();

  function mesej() {
    const d = p && J.dest(p.dest);
    const L = ['Assalamualaikum JJT, saya ingin membuat inquiry:', ''];
    if (p) L.push(`*Pakej:* ${d.pendek || d.nama}: ${p.nama}`, `*Jenis:* ${J.jenis(p.jenis).nama}`, ...(p.hari > 0 ? [`*Tempoh:* ${p.hari} hari ${p.malam} malam`] : []));
    if (adaPil()) L.push(`*Pilihan pakej:* ${pilTxt()}`);
    else L.push('*Pakej:* Belum pasti — mohon cadangan');
    L.push(`*Tarikh:* ${tarikhTxt()}`, `*Peserta:* ${paxTxt()}`, `*Bilik:* ${bilikTxt()}`);
    if (p) L.push(`*Anggaran:* ${J.rm(anggaran())}`);
    L.push('', `*Nama:* ${$('nama').value.trim()}`, `*Telefon:* ${$('tel').value.trim()}`);
    if ($('emel').value.trim()) L.push(`*Emel:* ${$('emel').value.trim()}`);
    if ($('negeri').value) L.push(`*Negeri:* ${$('negeri').value}`);
    if ($('nota').value.trim()) L.push(`*Permintaan khas:* ${$('nota').value.trim()}`);
    L.push('', 'Terima kasih.');
    return L.join('\n');
  }


  // Simpan salinan inquiry dalam database (untuk dashboard JJT)
  function simpan(m) {
    const fd = new FormData();
    const v = { jenis: 'pakej', pakej_id: p ? p.id : '', nama: $('nama').value.trim(), telefon: $('tel').value.trim(), emel: $('emel').value.trim(),
      negeri: $('negeri').value, tarikh: $('tarikh').value, peserta: paxTxt(), bilik: bilikTxt(), nota: $('nota').value.trim(), mesej: m, web: $('web').value,
      pax: JSON.stringify(pax), anggaran: anggaran() || '', setuju: $('setuju').checked ? 1 : '', pilihan: pilTxt() };
    Object.entries(v).forEach(([k, x]) => fd.append(k, x));
    JJT.lead(fd);
  }

  const modal = $('modal');
  $('f').onsubmit = e => {
    e.preventDefault();
    if (!valid()) { document.querySelector('.fld.bad').scrollIntoView({ behavior: 'smooth', block: 'center' }); return; }
    const m = mesej();
    modal.innerHTML = `<div class="mbox">
      <h3 id="mt"><svg viewBox="0 0 46 46"><circle cx="23" cy="23" r="23" fill="#25D366"/><use href="#wa" x="8" y="8" width="30" height="30" fill="#fff"/></svg>Semak mesej anda</h3>
      <p>Mesej ini akan dibuka dalam WhatsApp ke nombor JJT (${esc(J.WA_PAPAR)}). Anda masih boleh mengubahnya di sana.</p>
      <div class="bubble">${esc(m)}</div>
      <div class="mbtn"><button class="btn btn-ol" id="ubah">${ic('chev-l')}Ubah</button><a class="btn btn-gr" id="hantar" href="${waLink(m)}" target="_blank" rel="noopener"><svg class="w"><use href="#wa"/></svg>Hantar</a></div>
    </div>`;
    modal.classList.add('open');
    $('ubah').onclick = () => modal.classList.remove('open');
    $('hantar').onclick = () => { simpan(m); $('st3').classList.add('done'); $('st3').querySelector('i').innerHTML = ic('check'); setTimeout(() => modal.classList.remove('open'), 300); };
  };
  modal.onclick = e => { if (e.target === modal) modal.classList.remove('open'); };
  document.addEventListener('keydown', e => { if (e.key === 'Escape') modal.classList.remove('open'); });
})();
