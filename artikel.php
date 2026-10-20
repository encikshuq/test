<?php
require __DIR__ . '/inc/layout.php';
pastikan_hidup('blog', 'Blog & Tips Travel');
$b = blog($_GET['a'] ?? '');
if (!$b) tidak_dijumpai();
$d = $b['dest'] ? dest($b['dest']) : null;
$GLOBALS['WA_MSG'] = 'Assalamualaikum JJT, saya baru baca artikel "' . $b['tajuk'] . '"' . ($d ? ' dan berminat dengan pakej ke ' . nama_pendek($d) . '. Boleh bantu?' : ' dan ingin bertanya tentang pakej percutian.');
$lain = array_slice(array_values(array_filter(semua_blog(), fn($x) => $x['id'] !== $b['id'])), 0, 3);
page_head([
    'title' => $b['tajuk'] . ' — Blog JJT',
    'desc' => $b['meta'] ?: $b['ringkas'],
    'path' => 'blog/' . $b['id'],
    'og_img' => $b['img'] ?: 'images/hero.jpg',
    'og_type' => 'article',
    'page' => 'blog',
    'jsonld' => [
        '@context' => 'https://schema.org', '@type' => 'BlogPosting', 'headline' => $b['tajuk'], 'description' => $b['meta'] ?: $b['ringkas'],
        'image' => url_penuh($b['img'] ?: 'images/hero.jpg'), 'datePublished' => $b['tarikh'], 'mainEntityOfPage' => url_penuh('blog/' . $b['id']),
        'author' => ['@type' => 'Organization', 'name' => 'Jalan Jalan Travel'],
        'publisher' => ['@type' => 'Organization', 'name' => 'Jalan Jalan Travel', 'logo' => ['@type' => 'ImageObject', 'url' => url_penuh('images/logo.png')]],
    ],
]);
?>
    <section class="pb img" style="background-image:url(<?= h(img($b['img'])) ?>)">
      <?= crumb([['Utama', u()], ['Blog', u('blog')], ['Artikel']]) ?>
      <div class="tags"><span class="tag or"><?= ic('doc') ?><?= h($b['kat']) ?></span></div>
      <h1><?= h($b['tajuk']) ?></h1>
    </section>
    <div class="blay">
      <article class="card art">
        <div class="art-meta"><span><?= ic('cal') ?><?= tarikh_bm($b['tarikh']) ?></span><span><?= ic('clock') ?><?= (int)$b['minit'] ?> min bacaan</span><span><?= ic('users') ?>Pasukan JJT</span></div>
<?= $b['isi'] /* HTML artikel (ditulis oleh admin JJT) */ ?>

<?php if ($d): $dn = nama_pendek($d); ?>
        <div class="art-cta"><div><b>Rancang trip <?= h($dn) ?> anda</b><span>Lihat pakej dengan itinerari harian dan makan halal tersusun.</span></div>
          <div class="btns"><a class="btn btn-or" href="<?= h(url_dest($d['id'])) ?>">Lihat Pakej <?= h($dn) ?> <?= ic('chev') ?></a><a class="btn btn-ol" href="<?= h(wa_link('Assalamualaikum JJT, saya baca artikel "' . $b['tajuk'] . '" dan nak tanya tentang pakej ' . $dn . '.')) ?>" target="_blank" rel="noopener"><svg class="w"><use href="#wa"/></svg>Tanya JJT</a></div></div>
<?php else: ?>
        <div class="art-cta"><div><b>Sedia untuk trip seterusnya?</b><span>Terokai pakej percutian Muslim-friendly ke seluruh dunia.</span></div>
          <div class="btns"><a class="btn btn-or" href="<?= u('pakej') ?>">Lihat Semua Pakej <?= ic('chev') ?></a><a class="btn btn-ol" href="<?= h(wa_link()) ?>" target="_blank" rel="noopener"><svg class="w"><use href="#wa"/></svg>Tanya JJT</a></div></div>
<?php endif; ?>
      </article>
      <aside class="card side">
        <h3>Artikel Lain</h3>
<?php foreach ($lain as $x): ?>
        <a class="mini" href="<?= h(url_blog($x['id'])) ?>"><i style="background-image:url(<?= h(img($x['img'])) ?>)"></i><span><b><?= h($x['tajuk']) ?></b><small><?= tarikh_bm($x['tarikh']) ?></small></span></a>
<?php endforeach; ?>
        <a class="btn btn-ol btn-full" href="<?= u('blog') ?>">Semua Artikel <?= ic('chev') ?></a>
      </aside>
    </div>
<?php page_end();
