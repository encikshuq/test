<?php
/* Dashboard JJT: sesi, kebenaran & data panel */
declare(strict_types=1);
require dirname(__DIR__) . '/inc/db.php';
require dirname(__DIR__) . '/inc/fn.php';
require dirname(__DIR__) . '/inc/data.php';
require dirname(__DIR__) . '/inc/kandungan.php';
require dirname(__DIR__) . '/inc/schema.php';

header('X-Robots-Tag: noindex, nofollow');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: same-origin');

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
session_name('jjtpanel');
session_set_cookie_params(['lifetime' => 0, 'path' => BASE, 'secure' => $https, 'httponly' => true, 'samesite' => 'Lax']);
session_start();
if (isset($_SESSION['pengguna']) && (time() - ($_SESSION['aktif'] ?? 0)) > 8 * 3600) { $_SESSION = []; session_regenerate_id(true); }   // tamat selepas 8 jam tidak aktif
$_SESSION['aktif'] = time();
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));

/* Kemas kini struktur database sekali (kolum & jadual baharu untuk dashboard) */
function panel_migrasi(): void
{
    global $CONFIG;
    $v = '';
    try { $v = (string)q("SELECT v FROM tetapan WHERE k = 'skema'")->fetchColumn(); } catch (Throwable $e) {}
    if ($v === '11') return;
    jjt_migrasi(db(), ($CONFIG['driver'] ?? 'mysql') === 'sqlite' ? 'sqlite' : 'mysql');
    q("DELETE FROM tetapan WHERE k = 'skema'");
    q("INSERT INTO tetapan (k, v) VALUES ('skema', '11')");
}

function pengguna(): ?array { return $_SESSION['pengguna'] ?? null; }
function pentadbir(): bool { return (pengguna()['peranan'] ?? '') === 'Pentadbir'; }

/* Kebenaran staf (dipilih oleh Pentadbir). Data dashboard → kebenaran yang diperlukan */
const AKSES_SEMUA = ['pakej', 'destinasi', 'blog', 'testimoni', 'inquiry', 'inquiry_urus', 'laman', 'kandungan'];
const AKSES_LALAI = ['pakej', 'blog', 'testimoni', 'inquiry', 'inquiry_urus'];
const KUNCI_AKSES = ['pakej' => 'pakej', 'jenis' => 'pakej', 'kawasan' => 'pakej', 'dest' => 'destinasi', 'blog' => 'blog', 'testi' => 'testimoni', 'shorts' => 'testimoni',
    'inquiry' => 'inquiry_urus', 'utama' => 'laman', 'banner' => 'laman', 'tentangX' => 'kandungan', 'program' => 'kandungan', 'jawatan' => 'kandungan',
    'foundation' => 'kandungan', 'halaman' => 'kandungan'];   // tetapan, hidup, borang, footer: Pentadbir sahaja
function akses_dari(?string $json): array
{
    $a = $json ? json_decode($json, true) : null;
    return is_array($a) ? array_values(array_intersect(AKSES_SEMUA, $a)) : AKSES_LALAI;
}
function boleh(string $a): bool { return pentadbir() || in_array($a, pengguna()['akses'] ?? AKSES_LALAI, true); }

/* Data untuk dashboard, dalam bentuk yang sama seperti admin.html */
function panel_state(): array
{
    $j = fn($v) => $v === null || $v === '' ? null : json_decode((string)$v, true);
    $S = ['v' => 4];
    $S['pakej'] = array_map(fn($r) => [
        'id' => $r['id'], 'dest' => $r['dest'], 'jenis' => $r['jenis'], 'nama' => $r['nama'], 'hari' => (int)$r['hari'], 'malam' => (int)$r['malam'], 'harga' => (int)$r['harga'], 'hargaAsal' => (int)($r['harga_asal'] ?? 0) ?: null,
        'hotel' => (string)$r['hotel'], 'kumpulan' => (string)$r['kumpulan'], 'badge' => (string)($r['badge'] ?? ''), 'laluan' => $j($r['laluan']) ?? [], 'tarikh' => $j($r['tarikh']) ?? 'fleksibel',
        'sorotan' => $j($r['sorotan']) ?? [], 'itinerari' => $j($r['itinerari']) ?? [], 'termasuk' => $j($r['termasuk']) ?? [], 'tidak' => $j($r['tidak']) ?? [], 'nota' => $j($r['nota']) ?? [],
        'aktif' => (bool)(int)$r['aktif'], 'gambar' => $r['img'] ?? null, 'hargaDetail' => $j($r['harga_detail'] ?? null), 'tarikan' => $j($r['tarikan'] ?? null) ?? [], 'cuaca' => $j($r['cuaca'] ?? null) ?? []], q('SELECT * FROM pakej ORDER BY susunan, id')->fetchAll());
    $S['dest'] = array_map(function ($r) {
        $o = ['id' => $r['id'], 'nama' => $r['nama'], 'kawasan' => $r['kawasan'], 'img' => $r['img'] ?: null, 'musim' => (string)$r['musim'], 'matawang' => (string)$r['matawang'],
            'terbang' => (string)$r['terbang'], 'visa' => (string)$r['visa'], 'intro' => (string)$r['intro'], 'papar' => (bool)(int)($r['papar_utama'] ?? 1), 'urutan' => (int)$r['susunan']];
        if (!empty($r['pendek'])) $o['pendek'] = $r['pendek'];
        return $o;
    }, q('SELECT * FROM destinasi ORDER BY susunan, id')->fetchAll());
    $S['jenis'] = array_map(fn($r) => ['id' => $r['id'], 'nama' => $r['nama'], 'ikon' => $r['ikon'], 'ringkas' => (string)$r['ringkas']], q('SELECT * FROM jenis ORDER BY susunan, id')->fetchAll());
    $S['kawasan'] = array_map(fn($r) => ['id' => $r['id'], 'nama' => $r['nama']], q('SELECT * FROM kawasan ORDER BY susunan, id')->fetchAll());
    $S['testi'] = array_map(fn($r) => ['id' => (string)$r['id'], 'n' => $r['nama'], 'p' => (string)$r['pakej'], 'img' => $r['img'] ?: null, 'q' => (string)$r['teks'],
        'papar' => (bool)(int)$r['aktif'], 'sumber' => (string)($r['sumber'] ?? 'Website') ?: 'Website', 'g' => array_values((array)(json_decode((string)($r['gambar'] ?? ''), true) ?: []))], q('SELECT * FROM testimoni ORDER BY susunan, id')->fetchAll());
    $S['blog'] = array_map(fn($r) => ['id' => $r['id'], 'tajuk' => $r['tajuk'], 'kat' => (string)$r['kat'], 'img' => $r['img'] ?: null, 'tarikh' => (string)$r['tarikh'], 'minit' => (int)$r['minit'],
        'dest' => $r['dest'] ?: '', 'ringkas' => (string)$r['ringkas'], 'meta' => (string)$r['meta'], 'isi' => (string)$r['isi'], 'terbit' => (bool)(int)$r['aktif']],
        q('SELECT * FROM blog ORDER BY tarikh DESC, id')->fetchAll());
    $S['inquiry'] = boleh('inquiry') ? panel_inquiry() : [];
    $T = [];
    foreach (q('SELECT k, v FROM tetapan')->fetchAll() as $r) $T[$r['k']] = (string)$r['v'];
    $S['tetapan'] = ['wa' => $T['wa'] ?? '', 'emel' => $T['emel'] ?? '', 'alamat' => $T['alamat'] ?? '', 'waktu' => $T['waktu'] ?? '', 'notif' => $T['notif'] ?? '',
        'fb' => $T['fb'] ?? '', 'ig' => $T['ig'] ?? '', 'tiktok' => $T['tt'] ?? '', 'site' => $T['site_url'] ?? '', 'pratonton' => $T['pratonton'] ?? '', 'wa_teks' => $T['wa_teks'] ?? '', 'ga4' => $T['ga4'] ?? ''];
    foreach (KANDUNGAN_KEYS as $k) $S[$k] = kand($k);
    $S['hidup'] = array_map(fn($x) => (bool)$x, $S['hidup']);
    $S['users'] = pentadbir() ? panel_users() : [];
    return $S;
}

function panel_inquiry(): array
{
    return array_map(function ($r) {
        $hub = $r['jenis'] === 'mesej';
        $st = ucfirst(strtolower((string)$r['status'])) ?: 'Baru';
        $pax = $r['pax'] ?? null ? json_decode((string)$r['pax'], true) : null;
        return ['id' => (string)$r['id'], 'jenis' => $hub ? 'hubungi' : 'pakej', 'nama' => (string)$r['nama'], 'tel' => (string)$r['telefon'], 'emel' => (string)$r['emel'],
            'negeri' => (string)$r['negeri'], 'pakej' => (string)$r['pakej_id'], 'tarikh' => (string)$r['tarikh'], 'pax' => $hub ? null : ($pax ?: null), 'paxTeks' => (string)$r['peserta'],
            'bilik' => (string)$r['bilik'], 'perkara' => (string)($r['perkara'] ?? ''), 'pilihan' => (string)($r['pilihan'] ?? ''), 'nota' => (string)$r['nota'], 'setuju' => (bool)(int)($r['setuju'] ?? 0),
            'anggaran' => isset($r['anggaran']) ? ((int)$r['anggaran'] ?: null) : null, 'status' => in_array($st, ['Baru', 'Dihubungi', 'Tempahan', 'Tutup'], true) ? $st : 'Baru',
            'catatan' => (string)($r['catatan'] ?? ''), 'masa' => substr((string)$r['dicipta'], 0, 10), 'mesej' => (string)$r['mesej']];
    }, q('SELECT * FROM inquiry ORDER BY dicipta DESC, id DESC LIMIT 2000')->fetchAll());
}

function panel_users(): array
{
    return array_map(fn($r) => ['id' => (string)$r['id'], 'nama' => $r['nama'], 'emel' => $r['emel'], 'peranan' => $r['peranan'], 'akhir' => substr((string)$r['akhir'], 0, 10),
        'akses' => $r['peranan'] === 'Pentadbir' ? AKSES_SEMUA : akses_dari($r['akses'] ?? null)],
        q('SELECT id, nama, emel, peranan, akhir, akses FROM pengguna ORDER BY id')->fetchAll());
}

function json_keluar(array $o, int $kod = 200): void
{
    http_response_code($kod);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($o, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
