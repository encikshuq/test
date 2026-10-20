<?php
/* Semakan sistem inquiry — untuk Pentadbir sahaja (log masuk dashboard dahulu) */
require __DIR__ . '/inc.php';
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex');
if (!pengguna() || !pentadbir()) { http_response_code(403); exit('Sila log masuk ke dashboard sebagai Pentadbir dahulu, kemudian buka semula halaman ini.'); }

$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES);
$hasil = [];
$tambah = function (string $perkara, bool $ok, string $butiran = '') use (&$hasil) { $hasil[] = [$perkara, $ok, $butiran]; };

global $CONFIG;
$tambah('Sambungan database', true, ($CONFIG['driver'] ?? 'mysql') . ' · BASE = ' . BASE);

try {
    $lajur = array_keys(q('SELECT * FROM inquiry LIMIT 1')->fetch() ?: []);
    if (!$lajur) {   // jadual kosong: dapatkan nama lajur cara lain
        $st = db()->query('SELECT * FROM inquiry LIMIT 0');
        for ($i = 0; $i < $st->columnCount(); $i++) $lajur[] = $st->getColumnMeta($i)['name'] ?? '?';
    }
    $perlu = ['dicipta', 'jenis', 'pakej_id', 'nama', 'telefon', 'emel', 'negeri', 'tarikh', 'peserta', 'bilik', 'nota', 'mesej', 'status', 'ip', 'pax', 'anggaran', 'setuju', 'perkara', 'catatan'];
    $hilang = array_diff($perlu, $lajur);
    $tambah('Jadual inquiry', !$hilang, $hilang ? 'Lajur tiada: ' . implode(', ', $hilang) : count($lajur) . ' lajur lengkap');
} catch (Throwable $e) { $tambah('Jadual inquiry', false, $e->getMessage()); }

try {
    $n = (int)q('SELECT COUNT(*) FROM inquiry')->fetchColumn();
    $tambah('Bilangan inquiry dalam database', true, (string)$n);
} catch (Throwable $e) { $tambah('Bilangan inquiry', false, $e->getMessage()); }

try {   // ujian tulis sebenar, kemudian padam
    q('INSERT INTO inquiry (dicipta, jenis, pakej_id, nama, telefon, emel, negeri, tarikh, peserta, bilik, nota, mesej, status, ip, pax, anggaran, setuju, perkara) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
        [date('Y-m-d H:i:s'), 'pakej', '', 'UJIAN SISTEM', '0000000000', '', '', '', '', '', '', 'ujian', 'baru', 'ujian', null, null, 0, 'Ujian']);
    $id = (int)db()->lastInsertId();
    q('DELETE FROM inquiry WHERE id = ?', [$id]);
    $tambah('Ujian simpan inquiry (tulis & padam)', true, 'Berjaya');
} catch (Throwable $e) { $tambah('Ujian simpan inquiry (tulis & padam)', false, $e->getMessage()); }

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
try {
    $baru = (int)q('SELECT COUNT(*) FROM inquiry WHERE ip = ? AND dicipta >= ?', [$ip, date('Y-m-d H:i:s', time() - 600)])->fetchColumn();
    $tambah('Had cubaan dari IP anda (10 setiap 10 minit)', $baru < 10, "$baru dalam 10 minit terakhir · IP $ip");
} catch (Throwable $e) {}

$fail = dirname(__DIR__) . '/api/inquiry.php';
$tambah('Fail api/inquiry.php', is_file($fail), is_file($fail) ? 'Ada · dikemas kini ' . date('Y-m-d H:i', filemtime($fail)) : 'TIADA — upload semula ZIP');
$tambah('Masa server', true, date('Y-m-d H:i:s') . ' (' . date_default_timezone_get() . ')');

try { $akhir = q('SELECT id, dicipta, jenis, nama, perkara, pakej_id, ip FROM inquiry ORDER BY id DESC LIMIT 10')->fetchAll(); } catch (Throwable $e) { $akhir = []; }
?><!DOCTYPE html>
<html lang="ms"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex">
<title>Semakan Inquiry — JJT</title>
<style>
body{margin:0;font-family:system-ui,Segoe UI,Roboto,sans-serif;background:#EEF1F6;color:#13254A}
.w{max-width:760px;margin:24px auto;padding:0 16px}.c{background:#fff;border-radius:14px;box-shadow:0 6px 24px rgba(11,46,107,.08);padding:20px;margin-bottom:16px;overflow-x:auto}
h1{font-size:20px;color:#0B2E6B;margin:0 0 4px}h2{font-size:16px;color:#0B2E6B;margin:0 0 10px}p{color:#46536D;margin:0 0 6px}
table{border-collapse:collapse;width:100%;font-size:14px}td,th{padding:8px 6px;border-bottom:1px solid #E5EAF2;text-align:left;vertical-align:top}
.ok{color:#15803D;font-weight:700}.no{color:#B91C1C;font-weight:700}code{font-size:12.5px;word-break:break-all}a{color:#0B2E6B;font-weight:600}
</style></head><body><div class="w">
<div class="c"><h1>Semakan sistem inquiry</h1><p>Ambil screenshot halaman ini jika perlu bantuan teknikal. <a href="./#/inquiry">← Kembali ke dashboard</a></p></div>
<div class="c"><table><?php foreach ($hasil as [$p, $ok, $b]): ?><tr><td><?= $ok ? '<span class="ok">✓</span>' : '<span class="no">✗</span>' ?></td><td><b><?= $h($p) ?></b><br><code><?= $h($b) ?></code></td></tr><?php endforeach; ?></table></div>
<div class="c"><h2>10 inquiry terakhir dalam database</h2>
<?php if (!$akhir): ?><p>Tiada rekod.</p><?php else: ?><table><tr><th>ID</th><th>Masa</th><th>Jenis</th><th>Nama</th><th>Pakej / Perkara</th></tr>
<?php foreach ($akhir as $r): ?><tr><td><?= (int)$r['id'] ?></td><td><?= $h($r['dicipta']) ?></td><td><?= $h($r['jenis']) ?></td><td><?= $h($r['nama']) ?></td><td><?= $h($r['pakej_id'] ?: ($r['perkara'] ?? '')) ?></td></tr><?php endforeach; ?></table><?php endif; ?>
</div></div></body></html>
