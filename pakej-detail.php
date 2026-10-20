<?php
require __DIR__ . '/inc/layout.php';
pastikan_hidup('pakej', 'Pakej');

$p = pakej($_GET['p'] ?? '') ?: pakej_draf((string)($_GET['p'] ?? ''));
if (!$p) tidak_dijumpai();
$d = dest($p['dest']);
$j = jenis($p['jenis']);
$nama = nama_pendek($d);
$tetap = tarikh_tetap($p);
$tarikh = '';
if ($tetap) foreach ($p['tarikh'] as $t) if ($t['s'] !== 'penuh') { $tarikh = $t['t']; break; }
if ($tetap && isset($_GET['t'])) foreach ($p['tarikh'] as $t) if ($t['t'] === $_GET['t'] && $t['s'] !== 'penuh') $tarikh = $t['t'];   // dari jadual Series Departure

$MAKAN = [['S', 'Sarapan'], ['T', 'Makan tengah hari'], ['M', 'Makan malam']];
$STATUS = ['tersedia' => 'Tersedia', 'hampir' => 'Hampir penuh', 'penuh' => 'Penuh'];
$waMsg = 'Assalamualaikum JJT, saya berminat dengan pakej ' . $nama . ': ' . $p['nama'] . ' (' . durasi($p) . '). Boleh kongsikan maklumat lanjut?';
$GLOBALS['WA_MSG'] = $waMsg;
$pil = ada_pilihan($p);
$inq = url_inquiry($p['id'], $tarikh ?: null, $pil ? 'standard' : null);

page_head([
    'title' => $nama . ': ' . $p['nama'] . ($p['hari'] > 0 ? ' (' . $p['hari'] . 'H' . $p['malam'] . 'M)' : '') . ' — Jalan Jalan Travel',
    'desc' => $j['nama'] . ' ' . $nama . ($p['hari'] > 0 ? ' ' . $p['hari'] . ' hari ' . $p['malam'] . ' malam' : '') . ' dari ' . rm($p['harga']) . '. ' . implode(', ', array_slice($p['laluan'], 0, 5)) . '. ' . implode('. ', array_slice($p['sorotan'], 0, 3)) . '.',
    'path' => 'pakej/' . $p['id'],
    'og_img' => gambar_pakej($p) ?: 'images/hero.jpg',
    'noindex' => empty($p['aktif']),
    'page' => 'pakej',
    'body_class' => 'has-mbar no-cta',
    'jsonld' => [
        '@context' => 'https://schema.org', '@type' => 'TouristTrip', 'name' => $nama . ': ' . $p['nama'],
        'description' => implode('. ', $p['sorotan']), 'touristType' => $j['nama'],
        'itinerary' => ['@type' => 'ItemList', 'itemListElement' => array_map(fn($x, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $x], $p['laluan'], array_keys($p['laluan']))],
        'offers' => ['@type' => 'Offer', 'price' => $p['harga'], 'priceCurrency' => 'MYR', 'url' => url_penuh('pakej/' . $p['id'])],
        'provider' => ['@type' => 'TravelAgency', 'name' => 'Jalan Jalan Travel', 'url' => url_penuh()],
    ],
]);
?>
<?php $g = gambar_pakej($p); ?>    <section class="dh<?= $g ? '' : ' noimg' ?>"<?= $g ? ' style="background-image:url(' . h(img($g)) . ')"' : '' ?>>
      <?= crumb([['Utama', u()], ['Pakej', u('pakej')], [$nama, url_dest($d['id'])], [$p['nama']]]) ?>
      <div>
        <div class="tags"><span class="tag or"><?= ic($j['ikon']) ?><?= h($j['nama']) ?></span><?= $p['badge'] ? '<span class="tag">' . ic('spark') . h($p['badge']) . '</span>' : '' ?></div>
        <h1><?= h($nama) ?>: <?= h($p['nama']) ?></h1>
        <?= route_html($p['laluan']) ?>
      </div>
    </section>

<?php if ($pil): ?>
    <div class="card facts f3 pil-only">
      <div class="pil" role="radiogroup" aria-label="Pilih pakej">
        <small class="pil-t">Pilih pakej anda</small>
        <div class="pil-g"><?php foreach (PILIHAN_PAKEJ as $k => $x): ?>
          <button type="button" class="pil-b<?= $k === 'standard' ? ' on' : '' ?>" role="radio" aria-checked="<?= $k === 'standard' ? 'true' : 'false' ?>" data-k="<?= $k ?>"><span class="ci"><?= ic($x['ikon']) ?></span><b><?= h($x['nama']) ?></b><i><?= h($x['ket']) ?></i></button>
        <?php endforeach; ?></div>
      </div>
    </div>
<?php else: ?>
    <div class="card facts">
      <div><span class="ci"><?= ic('clock') ?></span><small>Tempoh</small><b><?= $p['hari'] ?> Hari <?= $p['malam'] ?> Malam</b></div>
      <div><span class="ci"><?= ic('users') ?></span><small>Kumpulan</small><b><?= h($p['kumpulan']) ?></b></div>
      <div><span class="ci"><?= ic('bed') ?></span><small>Penginapan</small><b><?= h($p['hotel']) ?></b></div>
      <div><span class="ci or"><?= ic('coin') ?></span><?= harga_html($p, 'or') ?></div>
    </div>
<?php endif; ?>

    <div class="dlay">
      <div>
<?php if ($p['tarikan'] || $p['cuaca']): ?>
        <section class="blk" id="tarikan">
          <h2 class="h2"><span class="ci or"><?= ic('pin') ?></span><span class="h2-t">Aktiviti dan Tempat <em>yang Menarik</em></span></h2>
<?php if ($p['tarikan']): $nT = count($p['tarikan']); ?>
          <div class="sld" data-n="<?= $nT ?>">
            <div class="sld-trk" tabindex="0" aria-label="Galeri tempat menarik"><?php foreach ($p['tarikan'] as $i => $t): ?>
              <figure class="sld-it"><?php if ($t['img']): ?><img src="<?= h(img($t['img'])) ?>" alt="<?= h($t['n']) ?>" loading="<?= $i < 2 ? 'eager' : 'lazy' ?>" width="800" height="600"><?php else: ?><span class="sld-ph"><?= ic('map') ?></span><?php endif; ?>
                <figcaption><?= ic('pin') ?><?= h($t['n']) ?></figcaption></figure><?php endforeach; ?>
            </div>
<?php if ($nT > 1): ?>
            <button class="sld-nav prev" type="button" aria-label="Sebelum"><?= ic('chev-l') ?></button><button class="sld-nav next" type="button" aria-label="Seterusnya"><?= ic('chev') ?></button>
            <div class="sld-dots" aria-hidden="true"><?php for ($i = 0; $i < $nT; $i++): ?><i<?= $i ? '' : ' class="on"' ?>></i><?php endfor; ?></div>
<?php endif; ?>
          </div>
<?php endif; ?>
<?php if ($p['cuaca']): ?>
          <div class="card cuaca"><b class="cu-t"><?= ic('sun') ?>Cuaca mengikut musim</b>
            <dl><?php foreach ($p['cuaca'] as $c): ?><div><dt><?= h($c['m']) ?></dt><dd><b><?= h($c['t']) ?></b><span><?= h($c['b']) ?></span></dd></div><?php endforeach; ?></dl>
          </div>
<?php endif; ?>
        </section>
<?php endif; ?>
<?php if ($p['sorotan']): /* seksyen kosong tidak dipaparkan */ ?>
        <section class="blk">
          <h2 class="h2 or"><span class="ci or"><?= ic('spark') ?></span>Highlight Pakej</h2>
          <div class="hlg"><?php foreach ($p['sorotan'] as $s): ?><div class="card"><span class="ci or"><?= ic('check') ?></span><?= h($s) ?></div><?php endforeach; ?></div>
        </section>
<?php endif; ?>
<?php if ($p['itinerari']): ?>
        <section class="blk" id="itin">
          <h2 class="h2"><span class="ci"><?= ic('f-route') ?></span>Itinerari Harian</h2>
          <div class="tl-tools"><button class="lnk" id="tog">Buka semua hari</button></div>
          <div class="tl">
<?php foreach ($p['itinerari'] as $i => $hr): $mk = explode(',', (string)($hr['makan'] ?? '')); ?>
            <div class="day">
              <div class="dnum"><small>HARI</small><?= $i + 1 ?></div>
              <details class="card"<?= $i === 0 ? ' open' : '' ?>>
                <summary><b><?= h($hr['tajuk']) ?></b><span class="tg"><?= ic('chev-d') ?></span></summary>
                <div class="dbody">
                  <ul><?php foreach ($hr['aktiviti'] as $a): ?><li><?= h($a) ?></li><?php endforeach; ?></ul>
                  <div class="meals"><?php foreach ($MAKAN as [$k, $n]): $y = in_array($k, $mk, true); ?><span class="<?= $y ? 'on' : '' ?>"><?= ic($y ? 'check' : 'x') ?><?= $n ?></span><?php endforeach; ?></div>
                </div>
              </details>
            </div>
<?php endforeach; ?>
          </div>
        </section>
<?php endif; ?>

        <section class="blk">
          <h2 class="h2"><span class="ci"><?= ic('cal') ?></span>Tarikh Berlepas</h2>
<?php if ($tetap): ?>
          <div class="dates" id="dates"><?php foreach ($p['tarikh'] as $t): $full = $t['s'] === 'penuh'; ?>
            <button class="card dt<?= $full ? ' full' : '' ?>" data-t="<?= h($t['t']) ?>" <?= $full ? 'disabled' : '' ?>>
              <b><?= tarikh_bm($t['t']) ?></b><small><?= $full ? 'Tiada kekosongan' : (int)$t['k'] . ' tempat lagi' ?></small>
              <span class="st-p st-<?= h($t['s']) ?>"><?= $STATUS[$t['s']] ?? h($t['s']) ?></span>
            </button><?php endforeach; ?>
          </div>
<?php else: ?>
          <div class="card flex-note"><span class="ci or"><?= ic('cal') ?></span><div><b>Tarikh fleksibel.</b> Pakej <?= h($j['nama']) ?> boleh berlepas pada tarikh pilihan anda. Nyatakan tarikh dalam borang inquiry dan kami akan semak kekosongan.</div></div>
<?php endif; ?>
        </section>

<?php if ($p['termasuk'] || $p['tidak']): ?>
        <section class="blk">
          <h2 class="h2"><span class="ci"><?= ic('doc') ?></span>Termasuk &amp; Tidak Termasuk</h2>
          <div class="inc">
<?php if ($p['termasuk']): ?>            <div class="card y"><h4>Harga termasuk</h4><ul><?php foreach ($p['termasuk'] as $x): ?><li><?= ic('check') ?><?= h($x) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<?php if ($p['tidak']): ?>            <div class="card n"><h4>Tidak termasuk</h4><ul><?php foreach ($p['tidak'] as $x): ?><li><?= ic('x') ?><?= h($x) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
          </div>
        </section>
<?php endif; ?>

<?php $hd = $p['harga_detail']; if ($hd && ($hd['baris'] || $hd['lain'])): $nk = max(1, count($hd['kol'])); $hv = fn($v) => preg_match('/^\d+$/', trim((string)$v)) ? rm((int)$v) : h($v); ?>
        <section class="blk" id="harga">
          <h2 class="h2"><span class="ci or"><?= ic('coin') ?></span>Butiran Harga</h2>
          <div class="card hrg"><div class="hrg-w"><table>
<?php if ($hd['kol']): ?>
            <thead><tr><th scope="col"><span class="sr">Kategori</span></th><?php foreach ($hd['kol'] as $k): ?><th scope="col"><?= h($k) ?></th><?php endforeach; ?></tr></thead>
<?php endif; ?>
            <tbody><?php foreach ($hd['baris'] as $b): ?>
              <tr><th scope="row"><?= h($b['n']) ?><?php if ($b['s'] !== ''): ?><small><?= h($b['s']) ?></small><?php endif; ?></th><?php foreach ($b['v'] as $v): ?><td><?= $v === '' ? '—' : $hv($v) ?></td><?php endforeach; ?></tr><?php endforeach; ?>
<?php foreach ($hd['lain'] as $b): ?>
              <tr class="ln"><th scope="row"><?= h($b['n']) ?><?php if ($b['s'] !== ''): ?><small><?= h($b['s']) ?></small><?php endif; ?></th><td colspan="<?= $nk ?>"><?= $hv($b['v']) ?></td></tr><?php endforeach; ?>
            </tbody></table></div>
<?php if ($hd['nota'] !== ''): ?>
            <p class="hrg-n"><?= ic('info') ?><?= h($hd['nota']) ?></p>
<?php endif; ?>
          </div>
        </section>

<?php endif; ?>
<?php if ($p['nota']): ?>
        <section class="blk">
          <h2 class="h2"><span class="ci"><?= ic('info') ?></span>Nota Penting</h2>
          <ul class="card notes"><?php foreach ($p['nota'] as $x): ?><li><?= ic('info') ?><?= h($x) ?></li><?php endforeach; ?></ul>
        </section>
<?php endif; ?>
      </div>

      <aside class="card book" id="book">
        <div class="rowp"><div class="price"><?= harga_html($p) ?><i>/ seorang</i></div><?php if (durasi($p)): ?><span class="bdg nv" style="box-shadow:0 0 0 1px #D9E0EC"><?= durasi($p) ?></span><?php endif; ?></div>
        <div class="hr"></div>
<?php if ($tetap): ?>
        <div class="fld"><label class="lbl" for="bt">Tarikh berlepas</label><select class="in" id="bt"><?php foreach ($p['tarikh'] as $t): ?><option value="<?= h($t['t']) ?>" <?= $t['s'] === 'penuh' ? 'disabled' : '' ?><?= $t['t'] === $tarikh ? ' selected' : '' ?>><?= tarikh_bm($t['t']) ?> · <?= $STATUS[$t['s']] ?? h($t['s']) ?></option><?php endforeach; ?></select></div>
<?php else: ?>
        <div class="lbl" style="display:flex;gap:6px;align-items:center"><?= ic('cal') ?> Tarikh fleksibel — pilih di borang</div>
<?php endif; ?>
<?php if ($pil): ?>
        <div class="lbl bk-pil" id="bkPil"><?= ic('car') ?><span>Pakej <b>Standard</b> · Pengangkutan persendirian</span></div>
<?php endif; ?>
        <a class="btn btn-or btn-full" id="inq" href="<?= h($inq) ?>"><?= ic('send') ?>Buat Inquiry</a>
        <a class="btn btn-ol btn-full" id="bkWa" href="<?= h(wa_link($waMsg)) ?>" target="_blank" rel="noopener"><svg class="w"><use href="#wa"/></svg>Tanya di WhatsApp</a>
        <ul class="trust">
          <li><?= ic('check') ?>Tiada bayaran untuk inquiry</li>
          <li><?= ic('check') ?>Maklum balas dalam 1 hari bekerja</li>
          <li><?= ic('check') ?>Tour leader Muslim dari Malaysia</li>
        </ul>
      </aside>
    </div>
<?php
$after = '<div class="mbar" id="mbar"><a class="btn btn-or btn-full" id="inq2" href="' . h($inq) . '">' . ic('send') . 'Buat Inquiry</a></div>';
$base = json_encode(url_inquiry($p['id']));
$waBase = json_encode($waMsg);
$waNo = json_encode(tetapan('wa'));
$pilJs = json_encode(PILIHAN_PAKEJ, JSON_UNESCAPED_UNICODE);
$js = <<<JS
<script>
(function () {
  let tk = '', pk = document.querySelector('.pil-b.on') ? document.querySelector('.pil-b.on').dataset.k : '';
  const PIL = {$pilJs};
  const inqUrl = () => {$base} + (tk ? '&t=' + tk : '') + (pk ? '&k=' + pk : '');
  const pasang = () => { ['inq', 'inq2'].forEach(id => { const a = document.getElementById(id); if (a) a.href = inqUrl(); }); };
  function setTarikh(t) {
    tk = t;
    document.querySelectorAll('.dt').forEach(b => b.classList.toggle('on', b.dataset.t === t));
    const bt = document.getElementById('bt'); if (bt) bt.value = t;
    pasang();
  }
  document.querySelectorAll('.pil-b').forEach(b => b.onclick = () => {
    pk = b.dataset.k;
    document.querySelectorAll('.pil-b').forEach(x => { x.classList.toggle('on', x === b); x.setAttribute('aria-checked', x === b ? 'true' : 'false'); });
    const x = PIL[pk] || {}, bk = document.getElementById('bkPil');
    if (bk && x.nama) bk.innerHTML = ic(x.ikon) + '<span>Pakej <b>' + esc(x.nama) + '</b> · ' + esc(x.ket) + '</span>';
    const wa = document.getElementById('bkWa');
    if (wa && x.nama) wa.href = 'https://wa.me/' + {$waNo} + '?text=' + encodeURIComponent({$waBase} + ' Pilihan pakej: ' + x.nama + ' (' + x.ket + ').');
    pasang();
  });
  const pilOn = document.querySelector('.pil-b.on'); if (pilOn) pilOn.click(); else pasang();
  document.querySelectorAll('.dt:not(.full)').forEach(b => b.onclick = () => setTarikh(b.dataset.t));
  const bt = document.getElementById('bt'); if (bt) { bt.onchange = () => setTarikh(bt.value); if (bt.value) setTarikh(bt.value); }
  document.querySelectorAll('.sld').forEach(sl => {
    const trk = sl.querySelector('.sld-trk'), it = [...trk.children], dots = [...sl.querySelectorAll('.sld-dots i')];
    const lebar = () => it[0].getBoundingClientRect().width + parseFloat(getComputedStyle(trk).columnGap || 0);
    const kini = () => Math.round(trk.scrollLeft / lebar());
    const pergi = i => trk.scrollTo({ left: Math.max(0, Math.min(it.length - 1, i)) * lebar(), behavior: 'smooth' });
    const pv = sl.querySelector('.prev'), nx = sl.querySelector('.next');
    const segar = () => { const i = kini(), hujung = trk.scrollLeft + trk.clientWidth >= trk.scrollWidth - 12;
      dots.forEach((d, j) => d.classList.toggle('on', j === i)); if (pv) pv.disabled = trk.scrollLeft < lebar() / 3; if (nx) nx.disabled = hujung; };
    if (pv) pv.onclick = () => pergi(kini() - 1);
    if (nx) nx.onclick = () => pergi(kini() + 1);
    let t; trk.addEventListener('scroll', () => { clearTimeout(t); t = setTimeout(segar, 60); }, { passive: true });
    trk.addEventListener('keydown', e => { if (e.key === 'ArrowRight') { e.preventDefault(); pergi(kini() + 1); } if (e.key === 'ArrowLeft') { e.preventDefault(); pergi(kini() - 1); } });
    segar();
  });
  const tog = document.getElementById('tog');
  if (tog) tog.onclick = () => {
    const ds = [...document.querySelectorAll('.day details')];
    const buka = ds.some(x => !x.open);
    ds.forEach(x => x.open = buka);
    tog.textContent = buka ? 'Tutup semua hari' : 'Buka semua hari';
  };
  /* ikon HARI bertukar oren bila hari itu dibuka */
  document.querySelectorAll('.day details').forEach(d => { const f = () => d.parentNode.classList.toggle('on', d.open); d.addEventListener('toggle', f); f(); });
})();
</script>
JS;
page_end(['after' => $after, 'js' => $js]);
