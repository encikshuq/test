<?php
/* Borang Itinerari Custom: pelanggan isi citarasa trip, JJT sediakan itinerari & sebut harga (WhatsApp + dashboard) */
require __DIR__ . '/inc/layout.php';

$pilih = $_GET['d'] ?? '';
$GLOBALS['WA_MSG'] = 'Assalamualaikum JJT, saya ingin dapatkan itinerari custom untuk percutian saya. Boleh bantu?';

/* Senarai negara (ikut rantau) & tempat menarik bagi negara popular */
const NEGARA = [
    'Asia Timur' => ['Jepun', 'Korea Selatan', 'China', 'Taiwan', 'Hong Kong'],
    'Asia Tenggara' => ['Malaysia', 'Indonesia', 'Thailand', 'Vietnam', 'Singapura', 'Kemboja'],
    'Asia Tengah' => ['Uzbekistan', 'Kazakhstan', 'Kyrgyzstan', 'Tajikistan'],
    'Asia Barat & Afrika Utara' => ['Turki', 'Jordan', 'Mesir', 'Maghribi', 'Emiriah Arab Bersatu'],
    'Eropah' => ['Rusia', 'United Kingdom', 'Perancis', 'Switzerland', 'Itali', 'Sepanyol', 'Jerman', 'Belanda', 'Austria', 'Bosnia & Herzegovina', 'Norway', 'Iceland'],
    'Amerika & Oseania' => ['Amerika Syarikat', 'Kanada', 'Peru', 'New Zealand', 'Australia'],
];
const TEMPAT = [
    'Jepun' => ['Tokyo', 'Gunung Fuji & Kawaguchiko', 'Disneyland / DisneySea', 'Osaka', 'Universal Studios Japan', 'Kyoto', 'Nara', 'Hokkaido'],
    'Korea Selatan' => ['Seoul', 'Pulau Nami', 'Everland / Lotte World', 'Gunung Seoraksan', 'Busan', 'Pulau Jeju'],
    'China' => ['Beijing & Tembok Besar', "Xi'an", 'Shanghai', 'Guilin', 'Zhangjiajie', 'Xinjiang'],
    'Malaysia' => ['Pulau Perhentian', 'Langkawi', 'Cameron Highlands', 'Kundasang, Sabah', 'Pulau Redang'],
    'Indonesia' => ['Bandung', 'Bali', 'Lombok', 'Yogyakarta', 'Aceh'],
    'Uzbekistan' => ['Tashkent', 'Samarkand', 'Bukhara', 'Khiva'],
    'Kazakhstan' => ['Almaty', 'Tasik Kaindy & Kolsai', 'Charyn Canyon', 'Astana'],
    'Turki' => ['Istanbul', 'Cappadocia', 'Pamukkale', 'Bursa', 'Antalya'],
    'Jordan' => ['Amman', 'Petra', 'Wadi Rum', 'Laut Mati', 'Makam Para Sahabat'],
    'Mesir' => ['Kaherah & Giza', 'Luxor', 'Aswan', 'Kapal Sungai Nil', 'Alexandria'],
    'Rusia' => ['Moscow', 'St. Petersburg', 'Kazan', 'Tasik Baikal', 'Murmansk (aurora)'],
    'United Kingdom' => ['London', 'Edinburgh', 'Lake District', 'Manchester'],
    'Perancis' => ['Paris', 'Disneyland Paris', 'Nice', 'Mont Saint-Michel'],
    'Switzerland' => ['Zurich', 'Lucerne', 'Interlaken', 'Gunung Titlis / Jungfrau'],
    'Itali' => ['Rom', 'Venice', 'Florence & Pisa', 'Milan'],
    'New Zealand' => ['Auckland', 'Rotorua', 'Queenstown', 'Milford Sound', 'Christchurch'],
    'Australia' => ['Sydney', 'Melbourne', 'Gold Coast', 'Perth'],
];
const TEMPOH = ['3 Hari 2 Malam', '4 Hari 3 Malam', '5 Hari 4 Malam', '6 Hari 5 Malam', '7 Hari 6 Malam', '8 Hari 7 Malam', '9 Hari 8 Malam', '10 Hari 9 Malam', 'Lebih 10 hari', 'Belum pasti'];
const BAJET = ['Bawah RM 5,000', 'RM 5,000 – RM 8,000', 'RM 8,000 – RM 12,000', 'RM 12,000 – RM 18,000', 'Lebih RM 18,000', 'Belum pasti'];
const KHIDMAT = [
    ['penuh', 'Pakej Penuh', 'Tiket + hotel + tour', 'plane'],
    ['ground', 'Ground Tour', 'Hotel + tour (tiket sendiri)', 'bed'],
    ['tour', 'Tour Sahaja', 'Tour leader / pemandu', 'users'],
];
const MUSLIM = ['Makanan halal sepanjang trip', 'Hotel berhampiran masjid / surau', 'Waktu solat dalam jadual', 'Tour leader Muslim', 'Kerusi roda / mesra warga emas'];
$BULAN = ['Januari', 'Februari', 'Mac', 'April', 'Mei', 'Jun', 'Julai', 'Ogos', 'September', 'Oktober', 'November', 'Disember'];
$bln = [];
for ($i = 1; $i <= 15; $i++) { $t = strtotime(date('Y-m-01') . " +$i month"); $bln[] = $BULAN[(int)date('n', $t) - 1] . ' ' . date('Y', $t); }
$NEGERI = kand('borang')['negeri'] ?? ['Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Perak', 'Perlis', 'Pulau Pinang', 'Sabah', 'Sarawak', 'Selangor', 'Terengganu', 'W.P. Kuala Lumpur', 'W.P. Putrajaya', 'W.P. Labuan'];

page_head([
    'title' => 'Itinerari Custom Percuma — Jalan Jalan Travel',
    'desc' => 'Rancang percutian Muslim-friendly ikut citarasa anda. Isi borang ringkas dan konsultan JJT akan sediakan itinerari custom serta sebut harga — percuma, tiada paksaan untuk menempah.',
    'path' => 'itinerari',
    'page' => 'itinerari',
]);
$pax = [['dewasa', 'Dewasa', '12 – 59 tahun', 2, 1], ['emas', 'Warga emas', '60 tahun ke atas', 0, 0], ['kanak', 'Kanak-kanak', '3 – 11 tahun', 0, 0], ['bayi', 'Bayi', 'Bawah 3 tahun', 0, 0]];
?>
    <section class="pb"><?= PLANE_PATH ?><?= crumb([['Utama', u()], ['Itinerari Custom']]) ?>
      <span class="it-tag"><?= ic('spark') ?> Percuma · Tiada paksaan menempah</span>
      <h1>Itinerari <em>Custom</em> Untuk Anda</h1>
      <p>Beritahu kami negara, tarikh dan citarasa anda. Konsultan JJT akan sediakan itinerari harian dan sebut harga khas melalui WhatsApp.</p>
    </section>

    <div class="card steps">
      <div class="stp now"><i>1</i>Isi citarasa trip</div><span class="sline"></span>
      <div class="stp"><i>2</i>Konsultan sediakan itinerari</div><span class="sline"></span>
      <div class="stp"><i>3</i>Sebut harga di WhatsApp</div>
    </div>

    <div class="flay" style="margin-top:calc(16*var(--u))">
      <form class="card fcard" id="f" novalidate><input id="web" name="web" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;opacity:0" aria-hidden="true">

        <div class="fsec">
          <h3><span class="n">1</span>Negara &amp; Tempat Menarik</h3>
          <div class="grid2">
            <div class="fld full" id="fDest"><label class="lbl" for="dest">Negara destinasi <em>*</em></label>
              <select class="in" id="dest"><option value="">Pilih negara</option>
                <?php foreach (NEGARA as $rantau => $senarai): ?><optgroup label="<?= h($rantau) ?>"><?php foreach ($senarai as $n): ?><option<?= $pilih === $n ? ' selected' : '' ?>><?= h($n) ?></option><?php endforeach; ?></optgroup><?php endforeach; ?>
                <option value="lain">Negara lain</option></select>
              <span class="ferr">Sila pilih negara.</span></div>
            <?php foreach (TEMPAT as $id => $senarai): ?>
            <div class="fld full tpt" data-dest="<?= h($id) ?>" hidden><span class="lbl">Tempat yang ingin dilawati <span class="muted">(boleh pilih lebih dari satu)</span></span>
              <div class="tgs"><?php foreach ($senarai as $t): ?><label class="tg"><input type="checkbox" name="tempat" value="<?= h($t) ?>"><span><?= ic('check') ?><?= h($t) ?></span></label><?php endforeach; ?></div></div>
            <?php endforeach; ?>
            <div class="fld full"><label class="lbl" for="lain" id="lainL">Negara / tempat lain dalam trip yang sama <span class="muted">(pilihan)</span></label>
              <input class="in" id="lain" placeholder="Cth: tambah Switzerland & Itali, Hokkaido musim salji…"></div>
          </div>
        </div>

        <div class="fsec">
          <h3><span class="n">2</span>Tempoh &amp; Tarikh</h3>
          <div class="grid2">
            <div class="fld" id="fTempoh"><label class="lbl" for="tempoh">Tempoh percutian <em>*</em></label>
              <select class="in" id="tempoh"><option value="">Pilih tempoh</option><?php foreach (TEMPOH as $t): ?><option><?= h($t) ?></option><?php endforeach; ?></select>
              <span class="ferr">Sila pilih tempoh.</span></div>
            <div class="fld" id="fBulan"><label class="lbl" for="bulan">Bila rancang untuk pergi? <em>*</em></label>
              <select class="in" id="bulan"><option value="">Pilih bulan</option><?php foreach ($bln as $b): ?><option><?= h($b) ?></option><?php endforeach; ?><option>Belum pasti</option></select>
              <span class="ferr">Sila pilih bulan.</span></div>
          </div>
        </div>

        <div class="fsec">
          <h3><span class="n">3</span>Bilangan Peserta</h3>
          <div class="pax it-pax">
            <?php foreach ($pax as [$k, $n, $s, $v, $min]): ?>
            <div><div><b><?= $n ?></b><small><?= $s ?></small></div>
              <div class="stepper"><button type="button" data-k="<?= $k ?>" data-d="-1" data-min="<?= $min ?>" aria-label="Kurang <?= $n ?>"><?= ic('minus') ?></button><output id="o-<?= $k ?>"><?= $v ?></output><button type="button" data-k="<?= $k ?>" data-d="1" aria-label="Tambah <?= $n ?>"><?= ic('plus') ?></button></div></div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="fsec">
          <h3><span class="n">4</span>Perkhidmatan &amp; Bajet</h3>
          <div class="fld"><span class="lbl">Perkhidmatan yang diperlukan</span>
            <div class="opts"><?php foreach (KHIDMAT as $i => [$v, $n, $s, $icn]): ?><label class="opt"><input type="radio" name="khidmat" value="<?= $v ?>"<?= $i === 0 ? ' checked' : '' ?>><span><?= ic($icn) ?><?= $n ?><small><?= $s ?></small></span></label><?php endforeach; ?></div></div>
          <div class="grid2" style="margin-top:calc(10*var(--u))">
            <div class="fld"><label class="lbl" for="bajet">Anggaran bajet seorang</label>
              <select class="in" id="bajet"><?php foreach (BAJET as $b): ?><option<?= $b === 'Belum pasti' ? ' selected' : '' ?>><?= h($b) ?></option><?php endforeach; ?></select></div>
            <div class="fld"><span class="lbl">Tiket penerbangan</span>
              <div class="seg2"><label><input type="radio" name="tiket" value="Belum beli" checked><span>Belum beli</span></label><label><input type="radio" name="tiket" value="Sudah beli"><span>Sudah beli</span></label></div></div>
          </div>
          <div class="grid2 it-flight" id="flight" hidden>
            <div class="fld"><label class="lbl" for="tba">Tarikh &amp; masa tiba</label><input class="in" id="tba" type="datetime-local"></div>
            <div class="fld"><label class="lbl" for="tbp">Tarikh &amp; masa pulang</label><input class="in" id="tbp" type="datetime-local"></div>
            <div class="fld full"><label class="lbl" for="lapangan">Lapangan terbang tiba / pulang</label><input class="in" id="lapangan" placeholder="Cth: Tiba Narita, pulang Kansai"></div>
          </div>
          <div class="fld" style="margin-top:calc(10*var(--u))"><span class="lbl">Keperluan Muslim-friendly</span>
            <div class="tgs"><?php foreach (MUSLIM as $i => $m): ?><label class="tg"><input type="checkbox" name="muslim" value="<?= h($m) ?>"<?= $i < 2 ? ' checked' : '' ?>><span><?= ic('check') ?><?= h($m) ?></span></label><?php endforeach; ?></div></div>
        </div>

        <div class="fsec">
          <h3><span class="n">5</span>Maklumat Anda</h3>
          <div class="grid2">
            <div class="fld" id="fNama"><label class="lbl" for="nama">Nama penuh <em>*</em></label><input class="in" id="nama" autocomplete="name" placeholder="Cth: Siti Aminah binti Ahmad"><span class="ferr">Sila isi nama penuh.</span></div>
            <div class="fld" id="fTel"><label class="lbl" for="tel">No. telefon (WhatsApp) <em>*</em></label><input class="in" id="tel" type="tel" inputmode="tel" autocomplete="tel" placeholder="Cth: 012-345 6789"><span class="ferr">Sila isi nombor telefon yang sah.</span></div>
            <div class="fld" id="fEmel"><label class="lbl" for="emel">Emel</label><input class="in" id="emel" type="email" autocomplete="email" placeholder="nama@contoh.com"><span class="ferr">Format emel tidak sah.</span></div>
            <div class="fld"><label class="lbl" for="negeri">Negeri</label><select class="in" id="negeri"><option value="">Pilih negeri</option><?php foreach ($NEGERI as $n): ?><option><?= h($n) ?></option><?php endforeach; ?></select></div>
            <div class="fld full"><label class="lbl" for="nota">Info tambahan <span class="muted">(pilihan)</span></label><textarea class="in" id="nota" placeholder="Cth: honeymoon, bawa ibu bapa, nak lebih masa membeli-belah, hotel pilihan…"></textarea></div>
          </div>
        </div>

        <div class="fsec">
          <div class="fld" id="fSetuju"><label class="chk"><input type="checkbox" id="setuju"><span>Saya bersetuju dengan <a href="<?= u('polisi/terma') ?>" target="_blank">Terma &amp; Syarat</a> dan <a href="<?= u('polisi/privasi') ?>" target="_blank">Polisi Privasi</a> JJT.</span></label><span class="ferr">Sila tandakan persetujuan.</span></div>
          <button class="btn btn-or btn-full it-go"><?= ic('send') ?>Ya! Saya Nak Itinerari Custom</button>
          <p class="hint" style="text-align:center;margin-top:calc(6*var(--u))">Anda akan melihat pratonton mesej sebelum ia dihantar ke WhatsApp JJT.</p>
        </div>
      </form>

      <aside class="card sum it-side">
        <h3>Kenapa itinerari custom JJT?</h3>
        <ul class="it-why">
          <li><span class="ci"><?= ic('f-route') ?></span><div><b>Ikut citarasa anda</b>Tempat, tempoh dan rentak perjalanan disusun mengikut keluarga anda.</div></li>
          <li><span class="ci"><?= ic('f-mosque') ?></span><div><b>Muslim-friendly</b>Makanan halal dan waktu solat diambil kira dalam setiap hari.</div></li>
          <li><span class="ci"><?= ic('coin') ?></span><div><b>Sebut harga telus</b>Kos dipecahkan dengan jelas sebelum anda membuat keputusan.</div></li>
          <li><span class="ci"><?= ic('f-shield') ?></span><div><b>Tiada paksaan</b>Itinerari dan sebut harga percuma. Tempah hanya bila anda bersedia.</div></li>
        </ul>
        <div class="it-sla"><?= ic('clock') ?><span>Konsultan akan hubungi anda dalam <b>1–2 hari bekerja</b>.</span></div>
        <a class="btn btn-gr btn-full" href="<?= h(wa_link()) ?>" target="_blank" rel="noopener"><svg class="w"><use href="#wa"/></svg>Tanya terus di WhatsApp</a>
      </aside>
    </div>
<?php
$js = <<<'JS'
<script>
(function () {
  const $ = id => document.getElementById(id), f = $('f'), modal = $('modal');
  const pax = { dewasa: 2, emas: 0, kanak: 0, bayi: 0 };
  const esc = s => String(s).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
  const destNama = () => $('dest').selectedOptions[0].textContent.trim();
  function tunjukTempat() {
    const v = $('dest').value;
    document.querySelectorAll('.tpt').forEach(el => { el.hidden = el.dataset.dest !== v; if (el.hidden) el.querySelectorAll('input').forEach(i => i.checked = false); });
    $('lainL').firstChild.textContent = v === 'lain' ? 'Nyatakan negara ' : 'Negara / tempat lain dalam trip yang sama ';
  }
  $('dest').onchange = () => { tunjukTempat(); if ($('fDest').classList.contains('bad')) chk('fDest', !!$('dest').value); };
  tunjukTempat();
  document.querySelectorAll('.stepper button').forEach(b => b.onclick = () => {
    const k = b.dataset.k, min = k === 'dewasa' ? 1 : 0; pax[k] = Math.max(min, Math.min(30, pax[k] + +b.dataset.d)); $('o-' + k).textContent = pax[k];
  });
  document.querySelectorAll('[name=tiket]').forEach(r => r.onchange = () => { $('flight').hidden = r.value !== 'Sudah beli' || !r.checked; });

  const paxTxt = () => [[pax.dewasa, 'dewasa'], [pax.emas, 'warga emas'], [pax.kanak, 'kanak-kanak'], [pax.bayi, 'bayi']].filter(x => x[0]).map(x => x[0] + ' ' + x[1]).join(', ');
  const checked = n => [...document.querySelectorAll(`[name=${n}]:checked`)].map(i => i.value);
  const khidmat = () => document.querySelector('[name=khidmat]:checked').closest('label').querySelector('span').childNodes[1].textContent.trim();
  const dt = v => v ? new Date(v).toLocaleString('ms-MY', { day: 'numeric', month: 'short', year: 'numeric', hour: 'numeric', minute: '2-digit' }) : '';

  function chk(id, ok) { $(id).classList.toggle('bad', !ok); const i = $(id).querySelector('.in'); if (i) i.classList.toggle('err', !ok); return ok; }
  function valid(onlyBad) {
    const r = [
      chk('fDest', !!$('dest').value), chk('fTempoh', !!$('tempoh').value), chk('fBulan', !!$('bulan').value),
      chk('fNama', $('nama').value.trim().length >= 3), chk('fTel', $('tel').value.replace(/\D/g, '').length >= 9),
      chk('fEmel', !$('emel').value.trim() || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($('emel').value.trim())), chk('fSetuju', $('setuju').checked)];
    return r.every(Boolean);
  }
  f.addEventListener('input', e => { const fl = e.target.closest('.fld'); if (fl && fl.classList.contains('bad')) valid(); });

  function mesej() {
    const tp = checked('tempat'), m = checked('muslim'), L = ['Assalamualaikum JJT, saya ingin dapatkan *itinerari custom*:', ''];
    L.push(`*Negara:* ${$('dest').value === 'lain' ? ($('lain').value.trim() || 'Negara lain') : destNama()}`);
    if (tp.length) L.push(`*Tempat pilihan:* ${tp.join(', ')}`);
    if ($('dest').value !== 'lain' && $('lain').value.trim()) L.push(`*Negara / tempat lain:* ${$('lain').value.trim()}`);
    L.push(`*Tempoh:* ${$('tempoh').value}`, `*Bila:* ${$('bulan').value}`, `*Peserta:* ${paxTxt()}`, `*Perkhidmatan:* ${khidmat()}`, `*Bajet seorang:* ${$('bajet').value}`);
    const tk = checked('tiket')[0]; L.push(`*Tiket penerbangan:* ${tk}`);
    if (tk === 'Sudah beli') { if ($('tba').value) L.push(`  Tiba: ${dt($('tba').value)}`); if ($('tbp').value) L.push(`  Pulang: ${dt($('tbp').value)}`); if ($('lapangan').value.trim()) L.push(`  Lapangan terbang: ${$('lapangan').value.trim()}`); }
    if (m.length) L.push(`*Keperluan:* ${m.join(', ')}`);
    L.push('', `*Nama:* ${$('nama').value.trim()}`, `*Telefon:* ${$('tel').value.trim()}`);
    if ($('emel').value.trim()) L.push(`*Emel:* ${$('emel').value.trim()}`);
    if ($('negeri').value) L.push(`*Negeri:* ${$('negeri').value}`);
    if ($('nota').value.trim()) L.push(`*Info tambahan:* ${$('nota').value.trim()}`);
    L.push('', 'Terima kasih.');
    return L.join('\n');
  }
  function simpan(m) {
    const ringkas = m.split('\n').slice(2, -2).filter(x => x && !/^\*(Nama|Telefon|Emel|Negeri):/.test(x)).join('\n').replace(/\*/g, '');
    const v = { jenis: 'pakej', pakej_id: '', perkara: 'Itinerari Custom', nama: $('nama').value.trim(), telefon: $('tel').value.trim(), emel: $('emel').value.trim(),
      negeri: $('negeri').value, tarikh: $('bulan').value, peserta: paxTxt(), bilik: '', nota: ringkas, mesej: m, web: $('web').value,
      pax: JSON.stringify({ dewasa: pax.dewasa + pax.emas, kanak: pax.kanak, bayi: pax.bayi }), setuju: $('setuju').checked ? 1 : '' };
    const fd = new FormData(); Object.entries(v).forEach(([k, x]) => fd.append(k, x));
    JJT.lead(fd);
  }
  f.onsubmit = e => {
    e.preventDefault();
    if (!valid()) { document.querySelector('.fld.bad').scrollIntoView({ behavior: 'smooth', block: 'center' }); return; }
    const m = mesej();
    modal.innerHTML = `<div class="mbox"><h3 id="mt"><svg viewBox="0 0 46 46"><circle cx="23" cy="23" r="23" fill="#25D366"/><use href="#wa" x="8" y="8" width="30" height="30" fill="#fff"/></svg>Semak permintaan anda</h3>
      <p>Mesej ini akan dibuka dalam WhatsApp ke nombor JJT (${esc(JJT.WA_PAPAR || '')}). Anda masih boleh mengubahnya di sana.</p>
      <div class="bubble">${esc(m)}</div>
      <div class="mbtn"><button class="btn btn-ol" id="ubah">Ubah</button><a class="btn btn-gr" id="hantar" href="${waLink(m)}" target="_blank" rel="noopener"><svg class="w"><use href="#wa"/></svg>Hantar</a></div></div>`;
    modal.classList.add('open');
    $('ubah').onclick = () => modal.classList.remove('open');
    $('hantar').onclick = () => { simpan(m); setTimeout(() => modal.classList.remove('open'), 300); };
  };
  modal.onclick = e => { if (e.target === modal) modal.classList.remove('open'); };
  document.addEventListener('keydown', e => { if (e.key === 'Escape') modal.classList.remove('open'); });
})();
</script>
JS;
page_end(['data' => true, 'after' => '<div class="modal" id="modal" role="dialog" aria-modal="true" aria-labelledby="mt"></div>', 'js' => $js]);
