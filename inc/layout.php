<?php
/* Header & footer untuk semua page dalaman */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/fn.php';
require_once __DIR__ . '/data.php';
require_once __DIR__ . '/kandungan.php';

const ASSET_V = '20261020a';

/* Menu ikut suis ON/OFF halaman di dashboard */
function menu_atas(string $page): string
{
    $M = [['utama', 'Utama', u()], ['pakej', 'Pakej', u('pakej')], ['tentang', 'Tentang Kami', u('tentang')], ['testimoni', 'Testimoni', u('testimoni')], ['blog', 'Blog', u('blog')], ['hubungi', 'Hubungi', u('hubungi')]];
    $o = '';
    foreach ($M as [$k, $l, $href]) if ($k === 'utama' || hidup($k)) $o .= '<a href="' . h($href) . '"' . ($k === $page ? ' class="on"' : '') . '>' . h($l) . '</a>';
    return '<nav class="hd-nav">' . $o . '</nav>';
}
function menu_drawer(bool $tutup = true, bool $utama = true): string
{
    $o = $tutup ? '<button class="dr-x" aria-label="Tutup" onclick="document.getElementById(\'dr\').classList.remove(\'open\')"><svg class="ic"><use href="#close"/></svg></button>' : '';
    $o .= '<a href="' . u() . '">Halaman Utama</a>';   // menggantikan "Semua Pakej" (permintaan client)
    if (hidup('pakej')) { foreach (semua_jenis() as $j) $o .= '<a href="' . h(u('pakej?jenis=' . $j['id'])) . '">' . h($j['nama']) . '</a>'; }
    foreach ([['tentang', 'Tentang Kami'], ['testimoni', 'Testimoni'], ['blog', 'Blog &amp; Tips Travel'], ['hubungi', 'Hubungi Kami']] as [$k, $l]) if (hidup($k)) $o .= '<a href="' . u($k) . '">' . $l . '</a>';
    return $o;
}
function pautan_footer(): string
{
    $o = '';
    foreach (kand('footer') as $f) {
        $href = ($f['t'] ?? 'd') === 'j' ? u('pakej?jenis=' . rawurlencode($f['v'])) : url_dest((string)$f['v']);
        $o .= '<li><a href="' . h($href) . '">' . h($f['l']) . '</a></li>';
    }
    return $o;
}

function seo_head(array $o): void
{
    $title = $o['title'] ?? 'Jalan Jalan Travel';
    $desc = $o['desc'] ?? 'Pakej percutian Muslim-friendly ke seluruh dunia bersama Jalan Jalan Travel — makan halal, solat diutamakan, itinerari santai.';
    if (mb_strlen($desc) > 165) $desc = rtrim(mb_substr($desc, 0, 162)) . '…';
    $path = $o['path'] ?? '';
    $og = $o['og_img'] ?? 'images/hero.jpg';
    $ogUrl = preg_match('~^https?://~', $og) ? $og : url_penuh($og);
    ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($title) ?></title>
<meta name="description" content="<?= h($desc) ?>">
<link rel="canonical" href="<?= h(url_penuh($path)) ?>">
<?php if (!empty($o['noindex'])): ?><meta name="robots" content="noindex">
<?php endif; ?>
<meta property="og:type" content="<?= h($o['og_type'] ?? 'website') ?>">
<meta property="og:site_name" content="Jalan Jalan Travel">
<meta property="og:locale" content="ms_MY">
<meta property="og:title" content="<?= h($title) ?>">
<meta property="og:description" content="<?= h($desc) ?>">
<meta property="og:url" content="<?= h(url_penuh($path)) ?>">
<meta property="og:image" content="<?= h($ogUrl) ?>">
<meta name="twitter:card" content="summary_large_image">
<link rel="icon" href="<?= u('favicon.ico') ?>" sizes="48x48">
<link rel="icon" type="image/png" sizes="192x192" href="<?= u('images/favicon-192.png') ?>">
<link rel="apple-touch-icon" href="<?= u('images/apple-touch-icon.png') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Caveat:wght@600&display=swap" rel="stylesheet">
<?php if (!empty($o['jsonld'])): ?>
<script type="application/ld+json"><?= json_encode($o['jsonld'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
<?php endif;
}

function page_head(array $o): void
{
    $page = $o['page'] ?? '';
    ?><!DOCTYPE html>
<html lang="ms">
<head>
<?php seo_head($o); ?>
<link rel="stylesheet" href="<?= u('assets/site.css') ?>?v=<?= ASSET_V ?>">
<?= analitik_head() ?>
</head>
<body data-page="<?= h($page) ?>"<?= !empty($o['body_class']) ? ' class="' . h($o['body_class']) . '"' : '' ?>>
<?php readfile(__DIR__ . '/sprite.svg'); ?>
<div class="page">
  <header class="hd">
    <a href="<?= u() ?>" class="logo"><img src="<?= u('images/logo.png') ?>" alt="JJT Jalan Jalan Travel"></a>
    <?= menu_atas($page) ?>
    <div class="hd-r">
      <a class="hd-cta" href="<?= h(wa_link()) ?>" target="_blank" rel="noopener"><svg><use href="#wa"/></svg>Tanya Sekarang</a>
      <button class="hd-menu" aria-label="Menu" onclick="document.getElementById('dr').classList.add('open')"><svg class="ic"><use href="#menu"/></svg></button>
    </div>
  </header>
  <main id="app">
<?php
}

function footer_html(bool $mainPage = false): void
{
    ?>
<?php if ($mainPage): /* seksyen WhatsApp "Masih tercari-cari" hanya di halaman Utama */ ?>
  <div class="cta">
    <svg class="wa" viewBox="0 0 46 46"><circle cx="23" cy="23" r="23" fill="#25D366"/><use href="#wa" x="8" y="8" width="30" height="30" fill="#fff"/></svg>
    <?php $U = kand('utama'); ?><div class="cta-t"><b><?= h($U['ctaTajuk']) ?></b><span><?= h($U['ctaAyat']) ?></span></div>
    <a class="btn-wa" href="<?= h(wa_link()) ?>" target="_blank" rel="noopener"><svg class="w" fill="#fff"><use href="#wa"/></svg><?= h($U['ctaButang']) ?><svg class="ic c"><use href="#chev"/></svg></a>
  </div>
<?php endif; ?>
  <footer class="ft<?= $mainPage ? '' : ' ft-nocta' ?>"<?= $mainPage ? ' id="hubungi"' : '' ?>>
   <div class="ft-grid">
    <div class="ft-r1">
      <div class="ft-logo">
        <img src="<?= u('images/logo-footer.png') ?>" alt="JJT Jalan Jalan Travel">
        <small><?= h(kand('tentangX')['slogan'] ?? '') ?: 'Beyond Ordinary Travel' ?></small>
        <div class="soc"><a href="<?= h(tetapan('fb')) ?>" target="_blank" rel="noopener" aria-label="Facebook Jalan Jalan Travel"><svg><use href="#fb"/></svg></a><a href="<?= h(tetapan('ig')) ?>" target="_blank" rel="noopener" aria-label="Instagram Jalan Jalan Travel"><svg><use href="#ig"/></svg></a><a href="<?= h(tetapan('tt')) ?>" target="_blank" rel="noopener" aria-label="TikTok Jalan Jalan Travel"><svg><use href="#tt"/></svg></a></div>
      </div>
      <div class="ft-c"><svg class="ic" style="stroke:none"><use href="#loc"/></svg><div><h4>ALAMAT</h4><p>Jalan Jalan Travel &amp; Tour<br>International Sdn Bhd<br><?= nl2br(h(tetapan('alamat'))) ?></p></div></div>
      <div class="ft-c"><svg class="ic" style="stroke:none"><use href="#phone"/></svg><div><h4>HUBUNGI KAMI</h4><p><?= h(tetapan('wa_papar')) ?> (WhatsApp)<br><?= str_replace([', ', ' ('], ['<br>', '<br>('], h(tetapan('waktu', 'Isnin – Jumaat, 10.00 pagi – 5.30 petang (Selain Cuti Umum)'))) ?></p><div class="mail"><svg class="ic ml" style="stroke:none"><use href="#mail"/></svg><span><?= h(tetapan('emel')) ?></span></div></div></div>
    </div>
    <div class="ft-r2">
<?php if (hidup('pakej')): ?>      <div class="ft-c"><svg class="ic" style="stroke:none"><use href="#plane"/></svg><div><h4>PAKEJ</h4><ul><?= pautan_footer() ?></ul></div></div><?php endif; ?>
      <div class="ft-c"><svg class="ic"><use href="#bldg"/></svg><div><h4>SYARIKAT</h4><ul><?php foreach ([['tentang', 'tentang', 'Tentang Kami'], ['tentang', 'tentang#kenapa', 'Kenapa Pilih JJT'], ['testimoni', 'testimoni', 'Testimoni Pelanggan'], ['blog', 'blog', 'Blog &amp; Tips Travel'], ['hubungi', 'hubungi', 'Hubungi Kami'], ['kerjaya', 'kerjaya', 'Kerjaya / Internship'], ['foundation', 'foundation', 'JJT Foundation']] as [$k, $hr, $l]) if (hidup($k)) echo '<li><a href="' . u($hr) . '">' . $l . '</a></li>'; ?></ul></div></div>
<?php if (hidup('polisi')): ?>      <div class="ft-c"><svg class="ic"><use href="#shield"/></svg><div><h4>POLISI</h4><ul><li><a href="<?= u('polisi/privasi') ?>">Privasi</a></li><li><a href="<?= u('polisi/penafian') ?>">Penafian</a></li><li><a href="<?= u('polisi/bayaran') ?>">Polisi Bayaran</a></li><li><a href="<?= u('polisi/terma') ?>">Terma &amp; Syarat</a></li></ul></div></div><?php endif; ?>
    </div>
   </div>
    <div class="ft-bottom">
      <div class="copy">© <?= date('Y') ?> Jalan Jalan Travel &amp; Tour International Sdn&nbsp;Bhd. <br>All rights reserved.</div>
      <a href="#" class="up" aria-label="Ke atas" onclick="scrollTo({top:0,behavior:'smooth'});return false"><svg class="ic"><use href="#up"/></svg></a>
    </div>
  </footer>
<?php
}

function page_end(array $o = []): void
{
    ?>
  </main>
<?php footer_html(); ?>
</div>
<?= $o['after'] ?? '' ?>
<div class="drawer" id="dr" onclick="if(event.target===this)this.classList.remove('open')">
  <nav>
    <?= menu_drawer() ?>
    <a class="dr-wa" href="<?= h(wa_link()) ?>" target="_blank" rel="noopener"><svg><use href="#wa"/></svg>Tanya di WhatsApp</a>
  </nav>
</div>
<?php if (!empty($o['data'])): ?><script>window.JJT_DATA = <?= data_js() ?>;</script>
<?php endif; ?>
<script src="<?= u('assets/app.js') ?>?v=<?= ASSET_V ?>"></script>
<?= $o['js'] ?? '' ?>
</body>
</html>
<?php
}

/* Halaman yang dimatikan di dashboard: papar "Akan Datang" (kecuali pratonton) */
function pastikan_hidup(string $k, string $nama): void
{
    if (hidup($k) || boleh_pratonton()) return;
    header('X-Robots-Tag: noindex');
    page_head(['title' => $nama . ' — Akan Datang · Jalan Jalan Travel', 'desc' => 'Halaman ini sedang disediakan.', 'path' => '', 'noindex' => true]);
    ?>
    <section class="pb"><?= PLANE_PATH ?><?= crumb([['Utama', u()], [$nama]]) ?>
      <h1><?= h($nama) ?> <em>Akan Datang</em></h1>
      <p>Halaman ini sedang disediakan. Sementara itu, pasukan kami sedia membantu anda melalui WhatsApp.</p>
    </section>
    <section class="sx">
      <div class="card" style="padding:calc(16*var(--u));display:flex;flex-wrap:wrap;gap:calc(8*var(--u))">
        <a class="btn btn-p" href="<?= u() ?>">Ke Laman Utama</a>
        <a class="btn btn-gr" href="<?= h(wa_link('Assalamualaikum JJT, saya ingin bertanya tentang ' . $nama . '.')) ?>" target="_blank" rel="noopener"><svg class="w"><use href="#wa"/></svg>Tanya di WhatsApp</a>
      </div>
    </section>
<?php
    page_end();
    exit;
}
