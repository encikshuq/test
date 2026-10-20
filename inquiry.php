<?php
require __DIR__ . '/inc/layout.php';
pastikan_hidup('inquiry', 'Borang Inquiry');

$p = pakej($_GET['p'] ?? '');
page_head([
    'title' => ($p ? 'Inquiry: ' . nama_pendek(dest($p['dest'])) . ' ' . $p['nama'] : 'Borang Inquiry') . ' — Jalan Jalan Travel',
    'desc' => 'Isi borang inquiry Jalan Jalan Travel untuk semakan kekosongan dan sebut harga pakej melalui WhatsApp — tiada bayaran untuk bertanya.',
    'path' => 'inquiry',
    'page' => 'pakej',
    'body_class' => 'no-cta',
]);
?>
    <noscript><section class="pb"><h1>Borang <em>Inquiry</em></h1><p>Sila aktifkan JavaScript, atau hubungi kami terus di WhatsApp <?= h(tetapan('wa_papar')) ?>.</p></section></noscript>
<?php
page_end([
    'data' => true,
    'after' => '<div class="modal" id="modal" role="dialog" aria-modal="true" aria-labelledby="mt"></div><template id="plane-tpl">' . PLANE_PATH . '</template>',
    'js' => '<script src="' . u('assets/inquiry.js') . '?v=' . ASSET_V . '"></script>',
]);
