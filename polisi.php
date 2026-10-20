<?php
require __DIR__ . '/inc/layout.php';
pastikan_hidup('polisi', 'Polisi');
$POL = [
    'privasi' => ['n' => 'Privasi', 'ic' => 'shield', 't' => 'Polisi Privasi', 's' => [
        ['Maklumat yang kami kumpul', 'Nama, nombor telefon, emel, maklumat pasport dan butiran perjalanan yang anda berikan semasa membuat inquiry atau tempahan.'],
        ['Tujuan penggunaan', 'Untuk memproses tempahan, menguruskan visa, tiket dan penginapan, serta menghubungi anda berkenaan perjalanan.'],
        ['Perkongsian maklumat', 'Maklumat hanya dikongsi dengan pihak ketiga yang berkaitan (syarikat penerbangan, hotel, kedutaan) untuk tujuan perjalanan anda.'],
        ['Hak anda', 'Anda boleh meminta salinan, pembetulan atau pemadaman data peribadi dengan menghubungi kami.']]],
    'penafian' => ['n' => 'Penafian', 'ic' => 'info', 't' => 'Penafian', 's' => [
        ['Ketepatan maklumat', 'Kami berusaha memastikan semua maklumat di laman ini tepat, namun harga, jadual dan itinerari boleh berubah tanpa notis.'],
        ['Pihak ketiga', 'JJT tidak bertanggungjawab atas kelewatan, pembatalan atau perubahan oleh syarikat penerbangan, hotel atau pihak berkuasa.'],
        ['Pautan luar', 'Laman ini mungkin mengandungi pautan ke laman luar yang tidak dikawal oleh JJT.']]],
    'bayaran' => ['n' => 'Polisi Bayaran', 'ic' => 'coin', 't' => 'Polisi Bayaran', 's' => [
        ['Deposit', 'Deposit diperlukan untuk mengesahkan tempahan. Jumlah deposit bergantung kepada pakej.'],
        ['Baki bayaran', 'Baki perlu dijelaskan sebelum tarikh akhir yang dinyatakan dalam invois.'],
        ['Kaedah bayaran', 'Pemindahan bank, FPX atau kaedah lain yang dinyatakan oleh JJT.'],
        ['Pembatalan & bayaran balik', 'Caj pembatalan dikenakan mengikut tempoh notis sebelum tarikh berlepas.']]],
    'terma' => ['n' => 'Terma & Syarat', 'ic' => 'doc', 't' => 'Terma & Syarat', 's' => [
        ['Tempahan', 'Tempahan hanya sah selepas deposit diterima dan pengesahan bertulis dikeluarkan oleh JJT.'],
        ['Dokumen perjalanan', 'Peserta bertanggungjawab memastikan pasport sah sekurang-kurangnya 6 bulan dan memenuhi syarat visa.'],
        ['Perubahan itinerari', 'JJT berhak mengubah itinerari atas faktor cuaca, keselamatan atau keadaan di luar kawalan.'],
        ['Insurans', 'Peserta digalakkan mempunyai insurans perjalanan yang mencukupi.']]],
];
$k = $_GET['p'] ?? 'privasi';
if (!isset($POL[$k])) $k = 'privasi';
$P = $POL[$k];
[$w1, $w2] = array_pad(explode(' ', $P['t'], 2), 2, '');
page_head([
    'title' => $P['t'] . ' — Jalan Jalan Travel',
    'desc' => $P['t'] . ' Jalan Jalan Travel & Tour International Sdn Bhd. Sila baca dengan teliti sebelum membuat tempahan.',
    'path' => 'polisi/' . $k,
    'page' => 'polisi',
]);
?>
    <section class="pb"><?= PLANE_PATH ?><?= crumb([['Utama', u()], ['Polisi', u('polisi/privasi')], [$P['n']]]) ?>
      <h1><?= h($w1) ?> <em><?= h($w2) ?></em></h1>
      <p>Sila baca dengan teliti sebelum membuat tempahan bersama Jalan Jalan Travel.</p>
    </section>
    <div class="sx" style="margin-top:0">
      <nav class="tabs"><?php foreach ($POL as $id => $x): ?><a href="<?= u('polisi/' . $id) ?>" class="<?= $id === $k ? 'on' : '' ?>"><?= ic($x['ic']) ?><?= h($x['n']) ?></a><?php endforeach; ?></nav>
    </div>
    <section class="sx">
      <article class="card prose">
        <p class="upd">Dikemaskini: Oktober 2026</p>
        <?= html_selamat(kand('halaman')[$k]['html']) ?>
      </article>
    </section>
<?php page_end(['js' => '<script>const tb=document.querySelector(".tabs"),on=tb.querySelector(".on");tb.scrollLeft=on.offsetLeft-(tb.clientWidth-on.offsetWidth)/2;</script>']);
