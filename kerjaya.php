<?php
require __DIR__ . '/inc/layout.php';
pastikan_hidup('kerjaya', 'Kerjaya');
$JOBS = array_values(array_filter(kand('jawatan'), fn($j) => !empty($j['buka'])));
page_head([
    'title' => 'Kerjaya & Internship — Jalan Jalan Travel',
    'desc' => 'Jawatan kosong dan peluang internship di Jalan Jalan Travel — sertai pasukan agensi pelancongan Muslim di Puncak Alam, Selangor.',
    'path' => 'kerjaya',
    'page' => 'kerjaya',
]);
?>

    <section class="pb"><?= PLANE_PATH ?><?= crumb([['Utama', u()], ['Kerjaya']]) ?>
      <h1>Kerjaya &amp; <em>Internship</em></h1>
      <?= html_selamat(kand('halaman')['kerjaya']['html']) ?>
    </section>
    <div class="sx" style="margin-top:calc(-40*var(--u));position:relative;z-index:3">
      <div class="vals">
        <div class="card"><span class="ci or"><?= ic('globe') ?></span><b>Peluang mengembara</b><p>Berpeluang menyertai trip dan mengenali destinasi baharu.</p></div>
        <div class="card"><span class="ci"><?= ic('f-group') ?></span><b>Pasukan mesra</b><p>Persekitaran kerja yang menyokong dan berasaskan nilai Islam.</p></div>
        <div class="card"><span class="ci"><?= ic('grad') ?></span><b>Belajar &amp; berkembang</b><p>Latihan industri pelancongan, jualan dan pengurusan trip.</p></div>
      </div>
    </div>
    <section class="sx">
      <div class="eb">Jawatan Kosong</div>
      <h2 class="tt">Sertai <em>Pasukan Kami</em></h2>
      <?php if (!$JOBS): ?>      <div class="card" style="padding:calc(14*var(--u));margin-top:calc(8*var(--u))"><p class="lead">Tiada jawatan kosong buat masa ini. Anda masih boleh menghantar resume untuk simpanan kami.</p></div>
<?php endif; foreach ($JOBS as $x): ?>
      <article class="card job"><div><h3><?= h($x['t']) ?></h3><div class="meta"><span><?= ic('brief') ?><?= h($x['j']) ?></span><span><?= ic('map') ?><?= h($x['l']) ?></span></div><p><?= h($x['d']) ?></p></div>
        <a class="btn btn-p" href="mailto:<?= h(tetapan('emel')) ?>?subject=<?= rawurlencode('Permohonan: ' . $x['t']) ?>">Mohon <?= ic('chev') ?></a></article>
<?php endforeach; ?>
      <div class="ph-box">Hantar resume ke <b><?= h(tetapan('emel')) ?></b> dengan tajuk jawatan.</div>
    </section>
<?php page_end();
