<?php
/* Pembantu paparan (setara dengan fungsi dalam layout.js asal) */

function h($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function u(string $path = ''): string
{
    return BASE . ltrim($path, '/');
}
function img(?string $path): string
{
    if (!$path) return '';
    return preg_match('~^(https?:)?//~', $path) ? $path : BASE . ltrim($path, '/');
}
function url_penuh(string $path = ''): string
{
    return rtrim(tetapan('site_url', ''), '/') . '/' . ltrim($path, '/');
}

/* URL laman */
function url_pakej(string $id): string { return u('pakej/' . rawurlencode($id)); }
function url_dest(string $id, ?string $jenis = null): string
{
    return u('destinasi/' . rawurlencode($id)) . ($jenis ? '?jenis=' . rawurlencode($jenis) : '');
}
function url_blog(string $id): string { return u('blog/' . rawurlencode($id)); }
function url_inquiry(?string $p = null, ?string $t = null, ?string $k = null): string
{
    $q = array_filter(['p' => $p, 't' => $t, 'k' => $k]);
    return u('inquiry') . ($q ? '?' . http_build_query($q) : '');
}

/* Pautan WhatsApp. Tanpa teks: guna mesej ikut halaman ($GLOBALS['WA_MSG']) atau mesej lalai dari dashboard. */
const WA_TEKS_LALAI = 'Assalamualaikum JJT, saya ingin bertanya tentang pakej percutian. Boleh bantu saya?';
function wa_link(string $text = ''): string
{
    if ($text === '') $text = (string)($GLOBALS['WA_MSG'] ?? '') ?: (tetapan('wa_teks', '') ?: WA_TEKS_LALAI);
    return 'https://wa.me/' . tetapan('wa') . '?text=' . rawurlencode($text);
}

function ic(string $id, string $cls = ''): string
{
    return '<svg class="ic ' . $cls . '"><use href="#' . $id . '"/></svg>';
}

function crumb(array $items): string
{
    $o = [];
    $n = count($items);
    foreach ($items as $i => $it) {
        $o[] = ($i < $n - 1 && isset($it[1]))
            ? '<a href="' . h($it[1]) . '">' . h($it[0]) . '</a>' . ic('chev')
            : '<span>' . h($it[0]) . '</span>';
    }
    return '<nav class="crumb">' . implode('', $o) . '</nav>';
}

function rm(int $n): string
{
    return 'RM ' . number_format($n);
}

function tarikh_bm(string $iso): string
{
    static $B = ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogo', 'Sep', 'Okt', 'Nov', 'Dis'];
    $t = strtotime($iso);
    return $t ? date('j', $t) . ' ' . $B[(int)date('n', $t) - 1] . ' ' . date('Y', $t) : $iso;
}

/* Harga di kad / page pakej: harga asal dipotong jika ada promo */
function harga_html(array $p, string $bCls = ''): string
{
    $asal = (int)($p['harga_asal'] ?? 0);
    return '<small>' . ($asal ? 'Harga promo' : 'Harga dari') . '</small>' . ($asal ? '<s class="h-asal">' . rm($asal) . '</s>' : '') . '<b' . ($bCls ? ' class="' . $bCls . '"' : '') . '>' . rm($p['harga']) . '</b>';
}

function durasi(array $p): string
{
    return (int)$p['hari'] > 0 ? $p['hari'] . 'H ' . $p['malam'] . 'M' : '';   // tempoh belum diisi: tidak dipaparkan
}

function route_html(array $arr, ?int $max = null): string
{
    if (!$arr) return '';   // laluan belum diisi
    $a = ($max && count($arr) > $max) ? array_merge(array_slice($arr, 0, $max), ['+' . (count($arr) - $max)]) : $arr;
    return '<div class="route"><svg class="pin"><use href="#pin"/></svg>' . implode(ic('chev'), array_map('h', $a)) . '</div>';
}

const PLANE_PATH = '<svg class="path" viewBox="0 0 200 80" fill="none"><path d="M4 70 C60 20 110 80 160 30" stroke="#F26A1F" stroke-width="2.5" stroke-dasharray="6 7" stroke-linecap="round"/><path d="M158 18 L192 26 L162 40 L166 29 Z" fill="#F26A1F"/></svg>';

/* Kad destinasi */
function dest_card(array $d, string $href, string $meta = ''): string
{
    $im = $d['img'] ? ' style="background-image:url(' . h(img($d['img'])) . ')"' : '';
    return '<a href="' . h($href) . '" class="d' . ($d['img'] ? '' : ' noimg') . '"' . $im . '>'
        . ($d['img'] ? '' : '<span class="ph">Foto akan datang</span>')
        . '<div class="d-l"><svg class="pin"><use href="#pin"/></svg><b>' . h(nama_pendek($d)) . ($meta !== '' ? '<span class="dm">' . preg_replace('~(dari RM[^<]*)$~u', '<span style="white-space:nowrap">$1</span>', h($meta)) . '</span>' : '') . '</b><span class="arr fill"><svg class="ic"><use href="#chev"/></svg></span></div></a>';
}

/* Kad pakej */
function pk_card(array $p): string
{
    $d = dest($p['dest']);
    $j = jenis($p['jenis']);
    $url = url_pakej($p['id']);
    $g = gambar_pakej($p);
    $im = $g ? ' style="background-image:url(' . h(img($g)) . ')"' : '';
    $hl = implode('', array_map(fn($s) => '<li>' . ic('check') . h($s) . '</li>', array_slice($p['sorotan'], 0, 3)));
    return '
    <article class="card pk">
      <a href="' . h($url) . '" class="pk-img' . ($g ? '' : ' noimg') . '"' . $im . ' aria-label="' . h($p['nama']) . '">
        ' . ($p['badge'] ? '<span class="bdg">' . h($p['badge']) . '</span>' : '') . '
        <span class="bdg nv jt">' . ic($j['ikon']) . ' ' . h($j['nama']) . '</span>
      </a>
      <div class="pk-b">
        <h3><a href="' . h($url) . '">' . h(nama_pendek($d)) . ': ' . h($p['nama']) . '</a></h3>
        <div class="meta">' . (durasi($p) ? '<span>' . ic('clock') . durasi($p) . '</span>' : '') . ($p['kumpulan'] ? '<span>' . ic('users') . h($p['kumpulan']) . '</span>' : '') . ($p['hotel'] ? '<span>' . ic('bed') . h($p['hotel']) . '</span>' : '') . '</div>
        ' . route_html($p['laluan'], 4) . '
        <ul class="hl pk-hl">' . $hl . '</ul>
        <div class="pk-f">
          <div class="price">' . harga_html($p) . '<i>/ seorang</i></div>
          <div class="pk-btns"><a class="btn btn-ol" href="' . h(url_inquiry($p['id'])) . '">Inquiry</a><a class="btn btn-p" href="' . h($url) . '">Lihat Pakej ' . ic('chev') . '</a></div>
        </div>
      </div>
    </article>';
}

/* Kad blog */
function blog_card(array $b): string
{
    return '<a class="bc" href="' . h(url_blog($b['id'])) . '" data-k="' . h($b['kat']) . '"><div class="bc-img" style="background-image:url(' . h(img($b['img'])) . ')"><span class="bc-k">' . h($b['kat']) . '</span></div>'
        . '<div class="bc-b"><small>' . tarikh_bm($b['tarikh']) . ' · ' . (int)$b['minit'] . ' min bacaan</small><h3>' . h($b['tajuk']) . '</h3><p>' . h($b['ringkas']) . '</p>'
        . '<span class="bc-r">Baca lagi ' . ic('chev') . '</span></div></a>';
}

function bintang(): string
{
    return str_repeat('<svg><use href="#star"/></svg>', 5);
}

function tidak_dijumpai(): void
{
    http_response_code(404);
    require ROOT . '/404.php';
    exit;
}

/* Skrip analitik dalam <head>: BASE untuk penjejak dashboard + Google Analytics 4 (jika ID diisi di Tetapan) */
function analitik_head(): string
{
    $o = '<script>window.JJT_BASE=' . json_encode(BASE) . ';</script>';
    $ga = strtoupper(trim((string)tetapan('ga4', '')));
    if (preg_match('/^G-[A-Z0-9]{4,16}$/', $ga)) {
        $o .= "\n<script async src=\"https://www.googletagmanager.com/gtag/js?id=$ga\"></script>"
            . "\n<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','$ga');</script>";
    }
    return $o;
}

/* Pilihan pakej (Private, Honeymoon, Adventure). Series Trip kekal dengan saiz kumpulan. */
const PILIHAN_PAKEJ = [
    'basic' => ['nama' => 'Basic', 'ket' => 'Public transport', 'ikon' => 'bus'],
    'standard' => ['nama' => 'Standard', 'ket' => 'Private transport', 'ikon' => 'car'],
    'premium' => ['nama' => 'Premium', 'ket' => 'Private transport + flight tickets', 'ikon' => 'plane'],
];
function ada_pilihan(array $p): bool { return ($p['jenis'] ?? '') !== 'series'; }
