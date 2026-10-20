<?php
/* Simpan inquiry / mesej dari borang website (dibaca oleh dashboard JJT nanti) */
require dirname(__DIR__) . '/inc/db.php';
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex');

header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('{"ok":false,"err":"kaedah"}'); }
if (trim($_POST['web'] ?? '') !== '') exit('{"ok":true}'); // perangkap bot

$f = fn($k, $max = 255) => mb_substr(trim((string)($_POST[$k] ?? '')), 0, $max);
$nama = $f('nama', 160);
$tel = $f('telefon', 40);
if (mb_strlen($nama) < 2 || strlen(preg_replace('/\D/', '', $tel)) < 9) { http_response_code(422); exit('{"ok":false,"err":"data"}'); }

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$baru = q('SELECT COUNT(*) FROM inquiry WHERE ip = ? AND dicipta >= ?', [$ip, date('Y-m-d H:i:s', time() - 600)])->fetchColumn();
if ((int)$baru >= 10) { http_response_code(429); exit('{"ok":false,"err":"had"}'); }

$jenis = in_array($_POST['jenis'] ?? '', ['pakej', 'mesej'], true) ? $_POST['jenis'] : 'pakej';
$pax = json_decode((string)($_POST['pax'] ?? ''), true);
$pax = is_array($pax) ? json_encode(['dewasa' => max(0, (int)($pax['dewasa'] ?? 0)), 'kanak' => max(0, (int)($pax['kanak'] ?? 0)), 'bayi' => max(0, (int)($pax['bayi'] ?? 0))]) : null;
$data = [date('Y-m-d H:i:s'), $jenis, $f('pakej_id', 120), $nama, $tel, $f('emel', 160), $f('negeri', 60), $f('tarikh', 40),
    $f('peserta', 120), $f('bilik', 40), $f('nota', 2000), $f('mesej', 4000), 'baru', $ip];
$penuh = fn() => q('INSERT INTO inquiry (dicipta, jenis, pakej_id, nama, telefon, emel, negeri, tarikh, peserta, bilik, nota, mesej, status, ip, pax, anggaran, setuju, perkara, pilihan) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
    array_merge($data, [$pax, (int)($_POST['anggaran'] ?? 0) ?: null, !empty($_POST['setuju']) ? 1 : 0, $f('perkara', 160), $f('pilihan', 80)]));
try {
    $penuh();
} catch (Throwable $e) {   // struktur database belum dikemas kini: kemas kini sekarang, kemudian cuba semula
    try {
        require_once dirname(__DIR__) . '/inc/schema.php';
        jjt_migrasi(db(), ($CONFIG['driver'] ?? 'mysql') === 'sqlite' ? 'sqlite' : 'mysql');
        $penuh();
    } catch (Throwable $e1) {
        try {
            q('INSERT INTO inquiry (dicipta, jenis, pakej_id, nama, telefon, emel, negeri, tarikh, peserta, bilik, nota, mesej, status, ip) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)', $data);
        } catch (Throwable $e2) {
            error_log('JJT inquiry gagal disimpan: ' . $e->getMessage() . ' | ' . $e1->getMessage() . ' | ' . $e2->getMessage());
            http_response_code(500);
            exit('{"ok":false,"err":"db"}');
        }
    }
}

/* Balas pelawat dahulu, kemudian hantar e-mel (supaya borang tidak menunggu pelayan e-mel) */
echo '{"ok":true}';
if (function_exists('fastcgi_finish_request')) fastcgi_finish_request(); elseif (function_exists('litespeed_finish_request')) litespeed_finish_request();

/* Notifikasi e-mel kepada staf (Tetapan → E-mel notifikasi inquiry) */
$ke = tetapan('notif');
if ($ke && filter_var($ke, FILTER_VALIDATE_EMAIL)) {
    $host = preg_replace('/[^a-z0-9.-]/i', '', $_SERVER['HTTP_HOST'] ?? 'jalantravel.com');
    $tajuk = '=?UTF-8?B?' . base64_encode(($jenis === 'mesej' ? 'Mesej baharu: ' : 'Inquiry baharu: ') . $nama) . '?=';
    $isi = $f('mesej', 4000) . "\n\n— Dihantar dari borang website. Urus di dashboard: https://" . $host . BASE . "jjt-panel/#/inquiry";
    @mail($ke, $tajuk, $isi, "From: Website JJT <noreply@$host>\r\nContent-Type: text/plain; charset=UTF-8");
}
