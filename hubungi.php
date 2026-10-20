<?php
require __DIR__ . '/inc/layout.php';
pastikan_hidup('hubungi', 'Hubungi Kami');
$alamat = trim(preg_replace('/\s*\n\s*/', ' ', tetapan('alamat')), ' .');
page_head([
    'title' => 'Hubungi Kami — Jalan Jalan Travel',
    'desc' => 'Hubungi Jalan Jalan Travel melalui WhatsApp ' . tetapan('wa_papar') . ', emel atau kunjungi pejabat kami di Bandar Hillpark, Puncak Alam, Selangor.',
    'path' => 'hubungi',
    'page' => 'hubungi',
]);
?>
    <section class="pb"><?= PLANE_PATH ?><?= crumb([['Utama', u()], ['Hubungi Kami']]) ?>
      <h1>Hubungi <em>Kami</em></h1>
      <?= html_selamat(kand('halaman')['hubungi']['html']) ?>
    </section>
    <div class="sx" style="margin-top:calc(-40*var(--u));position:relative;z-index:3">
      <div class="cgrid">
        <a class="card" href="<?= h(wa_link()) ?>" target="_blank" rel="noopener"><span class="ci gr"><svg><use href="#wa"/></svg></span><div><small>WhatsApp</small><b><?= h(tetapan('wa_papar')) ?></b><span>Cara paling cepat</span></div></a>
        <a class="card" href="tel:+<?= h(tetapan('wa')) ?>"><span class="ci"><?= ic('phone2') ?></span><div><small>Telefon</small><b><?= h(tetapan('wa_papar')) ?></b><span>Waktu pejabat</span></div></a>
        <a class="card" href="mailto:<?= h(tetapan('emel')) ?>"><span class="ci"><?= ic('mail2') ?></span><div><small>Emel</small><b style="font-size:max(12.5px,calc(7.6*var(--u)));overflow-wrap:anywhere;letter-spacing:-.01em"><?= h(tetapan('emel')) ?></b><span>Balasan 1–2 hari bekerja</span></div></a>
        <div class="card"><span class="ci or"><?= ic('clock') ?></span><div><small>Waktu operasi</small><b><?= str_replace([', ', ' ('], ['<br>', '<br><span style="display:inline;font-weight:500;color:#46536D">('], h(tetapan('waktu', 'Isnin – Jumaat, 10.00 pagi – 5.30 petang'))) . (strpos(tetapan('waktu', ''), ' (') !== false ? '</span>' : '') ?></b></div></div>
      </div>
    </div>
    <section class="sx">
      <div class="two" style="align-items:stretch">
        <div class="card" style="padding:calc(10*var(--u));display:flex;flex-direction:column">
          <h2 class="h2"><span class="ci"><?= ic('map') ?></span>Pejabat Kami</h2>
          <p class="lead" style="font-size:max(13px,calc(8.4*var(--u)))">Jalan Jalan Travel &amp; Tour International Sdn Bhd<br><?= h($alamat) ?>.</p>
          <div class="map" style="margin-top:calc(8*var(--u));flex:1;min-height:calc(160*var(--u))"><iframe loading="lazy" title="Peta pejabat JJT" src="https://maps.google.com/maps?q=<?= rawurlencode($alamat) ?>&amp;z=15&amp;output=embed"></iframe></div>
          <a class="btn btn-ol" style="margin-top:calc(8*var(--u))" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&amp;query=<?= rawurlencode($alamat) ?>"><?= ic('map') ?>Buka di Google Maps</a>
        </div>
        <form class="card fcard" id="f" novalidate>
          <h2 class="h2"><span class="ci or"><?= ic('send') ?></span>Hantar Mesej</h2>
          <input id="web" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;opacity:0" aria-hidden="true">
          <div class="grid2">
            <div class="fld full" id="fN"><label class="lbl" for="n">Nama <em>*</em></label><input class="in" id="n" autocomplete="name"><span class="ferr">Sila isi nama.</span></div>
            <div class="fld full" id="fT"><label class="lbl" for="t">No. telefon <em>*</em></label><input class="in" id="t" type="tel" inputmode="tel" autocomplete="tel"><span class="ferr">Sila isi nombor telefon yang sah.</span></div>
            <div class="fld full"><label class="lbl" for="s">Perkara</label><select class="in" id="s"><?php foreach (kand('borang')['perkara'] as $x): ?><option><?= h($x) ?></option><?php endforeach; ?></select></div>
            <div class="fld full" id="fM"><label class="lbl" for="m">Mesej <em>*</em></label><textarea class="in" id="m"></textarea><span class="ferr">Sila tulis mesej anda.</span></div>
          </div>
          <button class="btn btn-gr btn-full" style="margin-top:calc(10*var(--u));height:calc(34*var(--u))"><svg class="w"><use href="#wa"/></svg>Hantar melalui WhatsApp</button>
        </form>
      </div>
    </section>
<?php
$js = <<<'JS'
<script>
(function () {
  const $ = id => document.getElementById(id);
  const chk = (id, ok) => { $(id).classList.toggle('bad', !ok); $(id).querySelector('.in').classList.toggle('err', !ok); return ok; };
  $('f').onsubmit = e => {
    e.preventDefault();
    const ok = [chk('fN', $('n').value.trim().length >= 2), chk('fT', $('t').value.replace(/\D/g, '').length >= 9), chk('fM', $('m').value.trim().length >= 3)].every(Boolean);
    if (!ok) return;
    const msg = `Assalamualaikum JJT,\n\n*Perkara:* ${$('s').value}\n*Nama:* ${$('n').value.trim()}\n*Telefon:* ${$('t').value.trim()}\n\n${$('m').value.trim()}`;
    const fd = new FormData();
    Object.entries({ jenis: 'mesej', nama: $('n').value.trim(), telefon: $('t').value.trim(), perkara: $('s').value, nota: $('m').value.trim(), mesej: msg, web: $('web').value }).forEach(([k, v]) => fd.append(k, v));
    JJT.lead(fd);
    window.open(waLink(msg), '_blank', 'noopener');
  };
})();
</script>
JS;
page_end(['data' => true, 'js' => $js]);
