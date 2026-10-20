<?php
require_once __DIR__ . '/inc/layout.php';
http_response_code(404);
page_head(['title' => 'Page tidak dijumpai — Jalan Jalan Travel', 'desc' => 'Page yang anda cari tidak wujud.', 'path' => '', 'noindex' => true]);
?>
    <section class="pb"><?= PLANE_PATH ?><?= crumb([['Utama', u()], ['404']]) ?>
      <h1>Alamak, <em>tersesat!</em></h1>
      <p>Page yang anda cari tidak wujud atau sudah dipindahkan.</p>
    </section>
    <section class="sx">
      <div class="card" style="padding:calc(16*var(--u));display:flex;flex-wrap:wrap;gap:calc(8*var(--u))">
        <a class="btn btn-p" href="<?= u() ?>">Ke Laman Utama</a>
        <a class="btn btn-ol" href="<?= u('pakej') ?>">Lihat Semua Pakej</a>
        <a class="btn btn-gr" href="<?= h(wa_link()) ?>" target="_blank" rel="noopener"><svg class="w"><use href="#wa"/></svg>Tanya di WhatsApp</a>
      </div>
    </section>
<?php page_end();
