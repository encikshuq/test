<?php
require __DIR__ . '/inc/layout.php';
pastikan_hidup('foundation', 'JJT Foundation');
$F = kand('foundation');
page_head([
    'title' => 'JJT Foundation — Jalan Jalan Travel',
    'desc' => 'JJT Foundation ialah inisiatif kebajikan Jalan Jalan Travel untuk membantu golongan asnaf, anak yatim dan komuniti setempat.',
    'path' => 'foundation',
    'page' => 'foundation',
]);
?>

    <section class="pb"><?= PLANE_PATH ?><?= crumb([['Utama', u()], ['JJT Foundation']]) ?>
      <h1>JJT <em>Foundation</em></h1>
      <p>Sebahagian daripada setiap perjalanan anda kembali kepada masyarakat yang memerlukan.</p>
    </section>
    <div class="sx" style="margin-top:calc(-40*var(--u));position:relative;z-index:3">
      <div class="stats">
        <div class="card"><b><?= h($F['penerima'] ?: '—') ?></b><span>Penerima bantuan</span></div>
        <div class="card"><b><?= h($F['program'] ?: '—') ?></b><span>Program dijalankan</span></div>
        <div class="card"><b><?= h($F['sukarelawan'] ?: '—') ?></b><span>Sukarelawan</span></div>
        <div class="card"><b><?= h($F['negeri'] ?: '—') ?></b><span>Negeri terlibat</span></div>
      </div>
    </div>
    <section class="sx">
      <div class="two">
        <div>
          <div class="eb">Misi Kami</div>
          <h2 class="tt">Berkongsi <em>Rezeki</em></h2>
          <div style="margin-top:calc(8*var(--u))"><?= preg_replace('/<p(?=[\s>])/', '<p class="lead"', html_selamat(kand('halaman')['foundation']['html'])) ?></div>
        </div>
        <div class="ph-img" style="background-image:url(<?= u('images/mesir.jpg') ?>)"></div>
      </div>
    </section>
    <section class="sx">
      <div class="eb">Program</div>
      <h2 class="tt">Apa Yang <em>Kami Buat</em></h2>
      <div class="vals" style="margin-top:calc(10*var(--u))">
<?php foreach (kand('program') as $i => $x): ?>
        <div class="card"><span class="ci<?= $i % 3 === 0 ? ' or' : '' ?>"><?= ic(['hand', 'grad', 'f-mosque'][$i % 3]) ?></span><b><?= h($x['t']) ?></b><p><?= h($x['a']) ?></p></div>
<?php endforeach; ?>
      </div>
    </section>
    <section class="sx">
      <div class="card" style="padding:calc(16*var(--u));display:flex;flex-wrap:wrap;gap:calc(10*var(--u));align-items:center;justify-content:space-between">
        <div><b style="font-size:max(16px,calc(11*var(--u)));color:var(--navy)">Ingin menyumbang atau menjadi sukarelawan?</b><p class="lead" style="margin-top:calc(3*var(--u))">Hubungi kami untuk maklumat lanjut.</p></div>
        <a class="btn btn-gr" href="<?= h(wa_link('Assalamualaikum JJT, saya ingin tahu tentang JJT Foundation.')) ?>" target="_blank" rel="noopener"><svg class="w"><use href="#wa"/></svg>Hubungi via WhatsApp</a>
      </div>
    </section>
<?php page_end();
