<?php
require __DIR__ . '/inc/layout.php';
pastikan_hidup('pakej', 'Pakej');

$jenis = $_GET['jenis'] ?? 'semua';
if ($jenis !== 'semua' && !jenis($jenis)) $jenis = 'semua';
if ($jenis !== 'semua') $GLOBALS['WA_MSG'] = 'Assalamualaikum JJT, saya berminat dengan pakej ' . jenis($jenis)['nama'] . '. Boleh kongsikan pilihan yang ada?';
$kawasan = $_GET['kawasan'] ?? 'semua';
$susun = $_GET['susun'] ?? 'popular';
if (!in_array($susun, ['popular', 'murah', 'az'], true)) $susun = 'popular';

$pk = array_values(array_filter(semua_pakej(), fn($p) => $jenis === 'semua' || $p['jenis'] === $jenis));

/* Chip kawasan + bilangan destinasi */
$chips = [];
foreach (array_merge([['id' => 'semua', 'nama' => 'Semua']], semua_kawasan()) as $k) {
    $n = count(array_unique(array_map(fn($p) => $p['dest'], array_filter($pk, fn($p) => $k['id'] === 'semua' || (dest($p['dest'])['kawasan'] ?? '') === $k['id']))));
    if ($n || $k['id'] === 'semua') $chips[] = $k + ['n' => $n];
}
if (!in_array($kawasan, array_column($chips, 'id'), true)) $kawasan = 'semua';

/* Destinasi yang ada pakej */
$list = [];
foreach (semua_destinasi() as $d) {
    $ps = array_filter($pk, fn($p) => $p['dest'] === $d['id']);
    if (!$ps || ($kawasan !== 'semua' && $d['kawasan'] !== $kawasan)) continue;
    $list[] = ['d' => $d, 'n' => count($ps), 'min' => min(array_column($ps, 'harga')), 'pop' => (int)(bool)array_filter($ps, fn($p) => $p['badge'] === 'Popular')];
}
if ($susun === 'murah') usort($list, fn($a, $b) => $a['min'] <=> $b['min']);
elseif ($susun === 'az') usort($list, fn($a, $b) => strcmp($a['d']['nama'], $b['d']['nama']));
else usort($list, fn($a, $b) => ($b['pop'] - $a['pop']) ?: ((int)!!$b['d']['img'] - (int)!!$a['d']['img']));

/* Pakej popular (satu bagi setiap destinasi) */
$pop = $pk;
usort($pop, fn($a, $b) => (($b['badge'] === 'Popular') <=> ($a['badge'] === 'Popular')) ?: ((bool)$b['badge'] <=> (bool)$a['badge']));
$seen = [];
$pop = array_slice(array_values(array_filter($pop, function ($p) use (&$seen) {
    if (isset($seen[$p['dest']])) return false;
    return $seen[$p['dest']] = true;
})), 0, 3);

$qs = function (array $o) use ($jenis, $kawasan, $susun) {
    $a = array_merge(['jenis' => $jenis, 'kawasan' => $kawasan, 'susun' => $susun], $o);
    $a = array_filter($a, fn($v, $k) => !($v === 'semua' || ($k === 'susun' && $v === 'popular')), ARRAY_FILTER_USE_BOTH);
    return u('pakej') . ($a ? '?' . http_build_query($a) : '');
};
$jn = $jenis === 'semua' ? null : jenis($jenis);

page_head([
    'title' => ($jn ? 'Pakej ' . $jn['nama'] : 'Semua Pakej Percutian Muslim') . ' — Jalan Jalan Travel',
    'desc' => $jn ? $jn['ringkas'] . ' Pilih destinasi ' . $jn['nama'] . ' dengan itinerari harian lengkap, makan halal & tour leader Muslim.' : 'Senarai pakej percutian Muslim-friendly Jalan Jalan Travel: Private, Series, Honeymoon & Adventure Trip ke Jepun, Korea, Asia Tengah, Eropah, Jordan, Mesir dan banyak lagi.',
    'path' => 'pakej' . ($jn ? '?jenis=' . $jenis : ''),
    'page' => 'pakej',
]);
?>
    <section class="pb">
      <?= PLANE_PATH ?>
      <?= crumb([['Utama', u()], ['Pakej']]) ?>
      <h1>Pakej Percutian <em>Muslim</em></h1>
      <p>Pilih jenis trip, kemudian destinasi. Setiap destinasi ada beberapa pilihan pakej dengan itinerari harian yang lengkap.</p>
    </section>

    <nav class="tbar t5" id="tabs" aria-label="Jenis trip">
<?php foreach (array_merge([['id' => 'semua', 'nama' => 'Semua Pakej', 'ikon' => 't-all', 'ringkas' => 'Lihat semua pilihan']], semua_jenis()) as $j): ?>
        <a class="tb<?= $j['id'] === $jenis ? ' on' : '' ?>" href="<?= h($qs(['jenis' => $j['id']])) ?>">
          <span class="tb-ic"><svg class="ic"><use href="#<?= h($j['ikon']) ?>"/></svg></span>
          <b><?= str_replace(' ', '<br>', h($j['nama'])) ?></b>
          <small><?= h($j['ringkas']) ?></small>
        </a>
<?php endforeach; ?>
    </nav>

    <section class="sx">
      <div class="chips" id="chips"><?php foreach ($chips as $k): ?><a class="chip<?= $k['id'] === $kawasan ? ' on' : '' ?>" href="<?= h($qs(['kawasan' => $k['id']])) ?>"><?= h($k['nama']) ?><small><?= $k['n'] ?></small></a><?php endforeach; ?></div>
      <div class="resbar"><span id="count"><b><?= count($list) ?></b> destinasi untuk <?= h($jn ? $jn['nama'] : 'semua jenis trip') ?></span>
        <select class="sel" id="sort" aria-label="Susun" onchange="location.href=this.value">
          <option value="<?= h($qs(['susun' => 'popular'])) ?>"<?= $susun === 'popular' ? ' selected' : '' ?>>Paling popular</option>
          <option value="<?= h($qs(['susun' => 'murah'])) ?>"<?= $susun === 'murah' ? ' selected' : '' ?>>Harga terendah</option>
          <option value="<?= h($qs(['susun' => 'az'])) ?>"<?= $susun === 'az' ? ' selected' : '' ?>>Nama A–Z</option>
        </select>
      </div>
    </section>

    <section class="sx" style="margin-top:calc(10*var(--u))">
      <div class="dg" id="grid">
<?php if ($list): foreach ($list as $x): ?>
        <?= dest_card($x['d'], url_dest($x['d']['id'], $jenis !== 'semua' ? $jenis : null), $x['n'] . ' pakej · dari ' . rm($x['min'])) ?>
<?php endforeach; else: ?>
        <div class="empty" style="grid-column:1/-1"><?= ic('map') ?><br>Tiada pakej untuk pilihan ini buat masa sekarang.</div>
<?php endif; ?>
      </div>
    </section>

<?php if ($pop): ?>
    <section class="sx">
      <div class="eb">Pilihan Ramai</div>
      <h2 class="tt">Pakej <em>Popular</em></h2>
      <div id="pop"><?php foreach ($pop as $p) echo pk_card($p); ?></div>
    </section>
<?php endif; ?>
<?php page_end(['js' => '<script>(function(){const t=document.querySelector("#tabs .on");if(t&&t.scrollIntoView&&innerWidth<600){const n=t.parentNode;n.scrollLeft=t.offsetLeft-(n.clientWidth-t.offsetWidth)/2;}const c=document.querySelector("#chips .on");if(c){const n=c.parentNode;n.scrollLeft=c.offsetLeft-(n.clientWidth-c.offsetWidth)/2;}})();</script>']);
