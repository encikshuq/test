<?php
require __DIR__ . '/inc/layout.php';
pastikan_hidup('testimoni', 'Testimoni');
$T = semua_testimoni();
/* Video YouTube Shorts (diurus di dashboard: Testimoni → Video Shorts) */
$SH = array_values(array_filter(array_map(fn($x) => ($vi = video_info((string)($x['u'] ?? ''))) ? $vi + ['t' => (string)($x['t'] ?? ''), 'c' => (string)($x['c'] ?? '')] : null, (array)kand('shorts'))));
$inisial = fn($n) => implode('', array_map(fn($w) => mb_substr($w, 0, 1), array_slice(explode(' ', trim($n)), 0, 2)));
page_head([
    'title' => 'Testimoni Pelanggan — Jalan Jalan Travel',
    'desc' => 'Pengalaman sebenar pelancong yang telah menjelajah dunia bersama Jalan Jalan Travel — pakej Muslim-friendly ke Jepun, Turki, Korea, Jordan dan banyak lagi.',
    'path' => 'testimoni',
    'page' => 'testimoni',
]);
?>
    <section class="pb"><?= PLANE_PATH ?><?= crumb([['Utama', u()], ['Testimoni']]) ?>
      <h1>Apa Kata <em>Pelanggan</em></h1>
      <p>Pengalaman sebenar pelancong yang telah menjelajah dunia bersama JJT.</p>
    </section>
    <section class="sx">
      <div class="tgrid tmas" id="grid">
<?php foreach ($T as $n => $t): $g = $t['gambar']; $teks = trim((string)$t['teks']); ?>
      <article class="tc<?= $g ? ' tc-img' : '' ?>">
<?php if ($g): ?>
        <div class="tc-g" data-g="<?= h(json_encode(array_map(fn($p) => img($p), $g), JSON_UNESCAPED_SLASHES)) ?>">
          <button type="button" class="tc-g1" data-i="0" aria-label="Lihat gambar review <?= h($t['nama']) ?>"><img src="<?= h(img($g[0])) ?>" alt="Review <?= h($t['nama']) ?>" loading="<?= $n < 3 ? 'eager' : 'lazy' ?>"></button>
<?php if (count($g) > 1): ?>
          <div class="tc-gs"><?php foreach (array_slice($g, 1, 3) as $i => $p): ?><button type="button" data-i="<?= $i + 1 ?>" aria-label="Gambar <?= $i + 2 ?>"><img src="<?= h(img($p)) ?>" alt="" loading="lazy"><?php if ($i === 2 && count($g) > 4): ?><span>+<?= count($g) - 4 ?></span><?php endif; ?></button><?php endforeach; ?></div>
<?php endif; ?>
        </div>
<?php endif; ?>
        <div class="tc-h"><?php if ($t['img']): ?><img src="<?= h(img($t['img'])) ?>" alt=""><?php else: ?><span class="av-ph"><?= h($inisial($t['nama'])) ?></span><?php endif; ?>
          <div><div class="tc-n"><?= h($t['nama']) ?></div><div class="stars"><?= bintang() ?></div></div></div>
<?php if ($teks !== ''): ?>        <q><?= h($teks) ?></q>
<?php endif; ?>
      </article>
<?php endforeach; ?>
      </div>
    </section>

<?php if ($SH): ?>
    <section class="sx ts-sh" id="video">
      <div class="eb">Video Pelanggan</div>
      <h2 class="tt">Kenangan Dalam <em>Video</em></h2>
      <p class="ts-sh-p">Detik-detik sebenar pelanggan JJT sepanjang perjalanan. Video dimainkan secara automatik tanpa bunyi — tekan ikon speaker pada video untuk dengar.</p>
      <div class="sld shs">
        <div class="sld-trk shs-trk" tabindex="0" aria-label="Video pendek pelanggan"><?php foreach ($SH as $i => $s): ?>
          <div class="shs-it<?= $i ? '' : ' aktif' ?>" data-k="<?= $s['k'] ?>" data-id="<?= h($s['id']) ?>" data-t="<?= h($s['t'] ?: 'Video pelanggan JJT') ?>">
            <button type="button" class="shs-th" aria-label="Main video: <?= h($s['t'] ?: 'Video pelanggan JJT') ?>"><?php if ($s['c']): ?><img src="<?= h(img($s['c'])) ?>" alt="" loading="lazy"><?php elseif ($s['k'] === 'yt'): ?><img src="https://i.ytimg.com/vi/<?= h($s['id']) ?>/hqdefault.jpg" alt="" loading="lazy"><?php else: ?><span class="shs-ph"><svg class="ic"><use href="#tt"/></svg>TikTok</span><?php endif; ?><span class="shs-pl"><svg viewBox="0 0 24 24"><path d="M8 5.5v13l11-6.5z"/></svg></span></button>
<?php if ($s['t']): ?>            <b class="shs-t"><?= h($s['t']) ?></b><?php endif; ?>
          </div><?php endforeach; ?>
        </div>
        <button class="sld-nav prev" type="button" aria-label="Video sebelum"><?= ic('chev-l') ?></button><button class="sld-nav next" type="button" aria-label="Video seterusnya"><?= ic('chev') ?></button>
        <div class="sld-dots" aria-hidden="true"><?php foreach ($SH as $i => $s): ?><i<?= $i ? '' : ' class="on"' ?>></i><?php endforeach; ?></div>
      </div>
    </section>
<?php endif; ?>

    <div class="lb" id="lb" hidden role="dialog" aria-modal="true" aria-label="Gambar review">
      <button type="button" class="lb-x" aria-label="Tutup"><?= ic('close') ?></button>
      <button type="button" class="lb-n prev" aria-label="Sebelum"><?= ic('chev-l') ?></button>
      <img alt="Gambar review pelanggan">
      <button type="button" class="lb-n next" aria-label="Seterusnya"><?= ic('chev') ?></button>
      <span class="lb-c"></span>
    </div>
<?php page_end(['js' => '<script src="' . u('assets/testimoni.js') . '?v=' . ASSET_V . '" defer></script>']);
