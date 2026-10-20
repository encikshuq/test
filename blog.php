<?php
require __DIR__ . '/inc/layout.php';
pastikan_hidup('blog', 'Blog & Tips Travel');
$B = semua_blog();
$kats = array_values(array_unique(array_column($B, 'kat')));
page_head([
    'title' => 'Blog & Tips Travel — Jalan Jalan Travel',
    'desc' => 'Tips dan panduan percutian Muslim-friendly daripada Jalan Jalan Travel: makanan halal, solat, musim terbaik dan senarai semak sebelum berlepas.',
    'path' => 'blog',
    'page' => 'blog',
]);
?>
    <section class="pb">
      <?= PLANE_PATH ?>
      <?= crumb([['Utama', u()], ['Blog']]) ?>
      <h1>Blog &amp; <em>Tips Travel</em></h1>
      <p>Panduan praktikal untuk percutian Muslim-friendly — makanan halal, solat, musim terbaik dan persediaan sebelum berlepas.</p>
    </section>
    <section class="sx">
      <div class="chips" id="chips"><button class="chip on" data-k="Semua">Semua</button><?php foreach ($kats as $k): ?><button class="chip" data-k="<?= h($k) ?>"><?= h($k) ?></button><?php endforeach; ?></div>
      <div class="bgrid" id="grid">
<?php if ($B): foreach ($B as $b): ?>
        <?= blog_card($b) ?>

<?php endforeach; else: ?>
        <div class="empty" style="grid-column:1/-1"><?= ic('doc') ?><br>Artikel akan datang.</div>
<?php endif; ?>
      </div>
    </section>
<?php page_end(['js' => '<script>document.querySelectorAll("#chips .chip").forEach(c=>c.onclick=()=>{document.querySelectorAll("#chips .chip").forEach(x=>x.classList.toggle("on",x===c));document.querySelectorAll("#grid .bc").forEach(b=>b.hidden=c.dataset.k!=="Semua"&&b.dataset.k!==c.dataset.k);});</script>']);
