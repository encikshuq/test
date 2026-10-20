<?php
require __DIR__ . '/inc/layout.php';
pastikan_hidup('pakej', 'Pakej');

$d = dest($_GET['d'] ?? '');
if (!$d) tidak_dijumpai();
$all = pakej_dest($d['id']);
$ada = array_values(array_filter(semua_jenis(), fn($j) => (bool)array_filter($all, fn($p) => $p['jenis'] === $j['id'])));
$jenis = $_GET['jenis'] ?? 'semua';
if (!in_array($jenis, array_column($ada, 'id'), true)) $jenis = 'semua';
$ps = array_values(array_filter($all, fn($p) => $jenis === 'semua' || $p['jenis'] === $jenis));
/* Datang dari jenis trip tertentu (cth. Private Trip di main page): page ini hanya untuk jenis itu sahaja */
$J = $jenis !== 'semua' ? jenis($jenis) : null;
$min = $ps ? min(array_column($ps, 'harga')) : 0;
$nama = nama_pendek($d);
$GLOBALS['WA_MSG'] = 'Assalamualaikum JJT, saya berminat dengan pakej ke ' . $nama . '. Boleh kongsikan pilihan pakej dan tarikh yang ada?';

page_head([
    'title' => ($J ? $J['nama'] . ' ' . $d['nama'] : 'Pakej ' . $d['nama'] . ' Muslim-Friendly') . ' — Jalan Jalan Travel',
    'desc' => ($J ? $J['nama'] . ' ' : 'Pakej percutian ') . $d['nama'] . ($ps ? ' dari ' . rm($min) : '') . '. ' . $d['intro'] . ' Musim terbaik: ' . $d['musim'] . '.',
    'path' => 'destinasi/' . $d['id'] . ($J ? '?jenis=' . $J['id'] : ''),
    'og_img' => $d['img'] ?: 'images/hero.jpg',
    'page' => 'pakej',
    'body_class' => 'no-cta',
]);
?>
    <section class="pb<?= $d['img'] ? ' img' : '' ?>"<?= $d['img'] ? ' style="background-image:url(' . h(img($d['img'])) . ')"' : '' ?>>
      <?= $d['img'] ? '' : PLANE_PATH ?>
      <?= crumb($J ? [['Utama', u()], [$J['nama'], u('pakej?jenis=' . $J['id'])], [$nama]] : [['Utama', u()], ['Pakej', u('pakej')], [$nama]]) ?>
      <h1><?= $J ? h($J['nama']) : 'Pakej' ?> <em><?= h($d['nama']) ?></em></h1>
      <p><?= h($d['intro']) ?></p>
      <div class="tags"><span class="tag or"><?= ic($J ? $J['ikon'] : 'spark') ?><?= count($ps) ?> pilihan pakej<?= $J ? ' ' . h($J['nama']) : '' ?></span><?php if ($ps): ?><span class="tag"><?= ic('coin') ?>Dari <?= rm($min) ?></span><?php endif; ?></div>
    </section>

    <div class="sx" style="margin-top:calc(-40*var(--u));position:relative;z-index:3">
      <div class="info">
        <div class="card"><span class="ci"><?= ic('sun') ?></span><div><small>Musim terbaik</small><b><?= h($d['musim']) ?></b></div></div>
        <div class="card"><span class="ci"><?= ic('plane2') ?></span><div><small>Masa penerbangan</small><b><?= h($d['terbang']) ?></b></div></div>
        <div class="card"><span class="ci"><?= ic('coin') ?></span><div><small>Mata wang</small><b><?= h($d['matawang']) ?></b></div></div>
        <div class="card"><span class="ci"><?= ic('passport') ?></span><div><small>Visa</small><b><?= h($d['visa']) ?></b></div></div>
      </div>
    </div>

    <section class="sx">
      <div class="eb">Pilih Pakej</div>
      <h2 class="tt">Pilihan <?= $J ? h($J['nama']) : 'Pakej' ?> <em><?= h($nama) ?></em></h2>
      <p class="st" style="margin-bottom:calc(10*var(--u))">Bandingkan tempoh, laluan dan harga. Tekan "Lihat Pakej" untuk itinerari harian penuh.</p>
<?php if (!$J && count($ada) > 1): /* tab jenis hanya bila tiada jenis dipilih */ ?>
      <div class="seg" id="seg">
        <a href="<?= h(url_dest($d['id'])) ?>"<?= $jenis === 'semua' ? ' class="on"' : '' ?>>Semua <small>(<?= count($all) ?>)</small></a>
<?php foreach ($ada as $j): ?>
        <a href="<?= h(url_dest($d['id'], $j['id'])) ?>"<?= $jenis === $j['id'] ? ' class="on"' : '' ?>><?= ic($j['ikon']) ?><?= h($j['nama']) ?></a>
<?php endforeach; ?>
      </div>
<?php endif; ?>
      <div id="list">
<?php if ($ps): foreach ($ps as $p) echo pk_card($p); else: ?>
        <div class="empty"><?= ic('map') ?><br>Pakej untuk destinasi ini akan dikemas kini. <a class="lnk" href="<?= h(wa_link('Assalamualaikum JJT, saya berminat dengan pakej ' . $d['nama'] . '.')) ?>" target="_blank" rel="noopener">Tanya JJT di WhatsApp</a></div>
<?php endif; ?>
      </div>
    </section>
<?php page_end();
