<?php
/* API dashboard JJT */
require __DIR__ . '/inc.php';
panel_migrasi();

$a = $_GET['a'] ?? '';
$in = json_decode(file_get_contents('php://input') ?: 'null', true) ?: [];
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$kini = date('Y-m-d H:i:s');

/* ---------- Tanpa log masuk ---------- */
if ($a === 'login') {
    $gagal = (int)q('SELECT COUNT(*) FROM log_masuk WHERE ip = ? AND berjaya = 0 AND masa >= ?', [$ip, date('Y-m-d H:i:s', time() - 900)])->fetchColumn();
    if ($gagal >= 5) json_keluar(['ok' => false, 'err' => 'Terlalu banyak cubaan salah. Cuba lagi selepas 15 minit.'], 429);
    $emel = strtolower(trim((string)($in['emel'] ?? '')));
    $u = q('SELECT * FROM pengguna WHERE LOWER(emel) = ?', [$emel])->fetch();
    $ok = $u && password_verify((string)($in['pass'] ?? ''), (string)$u['hash']);
    q('INSERT INTO log_masuk (ip, emel, masa, berjaya) VALUES (?, ?, ?, ?)', [$ip, $emel, $kini, $ok ? 1 : 0]);
    if (!$ok) json_keluar(['ok' => false, 'err' => 'E-mel atau kata laluan salah (' . max(0, 4 - $gagal) . ' cubaan lagi).'], 401);
    session_regenerate_id(true);
    $_SESSION['pengguna'] = ['id' => (int)$u['id'], 'nama' => $u['nama'], 'emel' => $u['emel'], 'peranan' => $u['peranan'], 'akses' => akses_dari($u['akses'] ?? null), 'wajib' => (bool)(int)($u['wajib_tukar'] ?? 0)];
    q('UPDATE pengguna SET akhir = ? WHERE id = ?', [$kini, $u['id']]);
    json_keluar(['ok' => true]);
}
if ($a === 'setup') {   // akaun Pentadbir pertama (hanya bila belum ada pengguna)
    if ((int)q('SELECT COUNT(*) FROM pengguna')->fetchColumn() > 0) json_keluar(['ok' => false, 'err' => 'Akaun sudah wujud.'], 403);
    $nama = trim((string)($in['nama'] ?? '')); $emel = strtolower(trim((string)($in['emel'] ?? ''))); $pass = (string)($in['pass'] ?? '');
    if (!$nama || !filter_var($emel, FILTER_VALIDATE_EMAIL) || strlen($pass) < 8) json_keluar(['ok' => false, 'err' => 'Isi nama, e-mel sah dan kata laluan sekurang-kurangnya 8 aksara.'], 422);
    q('INSERT INTO pengguna (nama, emel, hash, peranan, akhir, dicipta) VALUES (?, ?, ?, ?, ?, ?)', [$nama, $emel, password_hash($pass, PASSWORD_DEFAULT), 'Pentadbir', $kini, $kini]);
    session_regenerate_id(true);
    $_SESSION['pengguna'] = ['id' => (int)db()->lastInsertId(), 'nama' => $nama, 'emel' => $emel, 'peranan' => 'Pentadbir'];
    json_keluar(['ok' => true]);
}
if ($a === 'logout') { $_SESSION = []; session_destroy(); json_keluar(['ok' => true]); }

/* ---------- Perlu log masuk + token ---------- */
if (!pengguna()) json_keluar(['ok' => false, 'err' => 'Sesi tamat. Sila log masuk semula.', 'login' => true], 401);
if ($a !== 'eksport' && !hash_equals($_SESSION['csrf'], (string)($_SERVER['HTTP_X_CSRF'] ?? ''))) json_keluar(['ok' => false, 'err' => 'Token tidak sah. Muat semula halaman.'], 403);
// kemas kini peranan dari database (jika diubah oleh Pentadbir lain)
$me = q('SELECT id, nama, emel, peranan, akses, wajib_tukar FROM pengguna WHERE id = ?', [pengguna()['id']])->fetch();
if (!$me) { $_SESSION = []; json_keluar(['ok' => false, 'err' => 'Akaun telah dibuang.', 'login' => true], 401); }
$_SESSION['pengguna'] = ['id' => (int)$me['id'], 'nama' => $me['nama'], 'emel' => $me['emel'], 'peranan' => $me['peranan'], 'akses' => akses_dari($me['akses'] ?? null), 'wajib' => (bool)(int)($me['wajib_tukar'] ?? 0)];
// kata laluan sementara: mesti tukar dahulu sebelum boleh guna dashboard
if ($_SESSION['pengguna']['wajib'] && !in_array($a, ['pass', 'state', 'inquiry'], true)) json_keluar(['ok' => false, 'err' => 'Sila tukar kata laluan sementara anda dahulu.', 'wajib' => true], 403);

$jsn = fn($v) => json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$str = fn($v, $max = 255) => mb_substr(trim((string)($v ?? '')), 0, $max);
$gambar = function ($p) {   // hanya laluan gambar dalam laman ini atau URL https
    $p = (string)($p ?? '');
    if ($p === '') return null;
    if (preg_match('~^(images|uploads)/[A-Za-z0-9._/-]+\.(jpe?g|png|webp|gif)$~i', $p) || preg_match('~^https://~', $p)) return $p;
    return null;
};
$hargaDetail = function ($h) use ($str) {   // jadual Butiran Harga
    if (!is_array($h)) return null;
    $kol = array_slice(array_values(array_map(fn($x) => $str($x, 40), (array)($h['kol'] ?? []))), 0, 6);
    $baris = [];
    foreach (array_slice((array)($h['baris'] ?? []), 0, 12) as $b) {
        if (!is_array($b) || trim((string)($b['n'] ?? '')) === '') continue;
        $v = array_map(fn($x) => $str($x, 30), array_slice(array_values((array)($b['v'] ?? [])), 0, count($kol)));
        $baris[] = ['n' => $str($b['n'], 80), 's' => $str($b['s'] ?? '', 80), 'v' => array_pad($v, count($kol), '')];
    }
    $lain = [];
    foreach (array_slice((array)($h['lain'] ?? []), 0, 8) as $b) if (is_array($b) && trim((string)($b['n'] ?? '')) !== '') $lain[] = ['n' => $str($b['n'], 80), 's' => $str($b['s'] ?? '', 80), 'v' => $str($b['v'] ?? '', 120)];
    if (!$kol && !$baris && !$lain) return null;
    return ['kol' => $kol, 'baris' => $baris, 'lain' => $lain, 'nota' => $str($h['nota'] ?? '', 400)];
};
$tarikan = fn($t) => array_values(array_filter(array_map(fn($x) => is_array($x) && trim((string)($x['n'] ?? '')) !== '' ? ['n' => $str($x['n'], 80), 'img' => $gambar($x['img'] ?? null)] : null, array_slice((array)$t, 0, 10))));
$cuaca = fn($t) => array_values(array_filter(array_map(fn($x) => is_array($x) && trim((string)($x['m'] ?? '')) !== '' ? ['m' => $str($x['m'], 40), 't' => $str($x['t'] ?? '', 40), 'b' => $str($x['b'] ?? '', 60)] : null, array_slice((array)$t, 0, 6))));
$slug = fn($s) => preg_match('/^[a-z0-9][a-z0-9-]{0,118}$/', (string)$s) ? (string)$s : null;

switch ($a) {
case 'state':
    json_keluar(['ok' => true, 'state' => panel_state()]);

case 'inquiry':
    json_keluar(['ok' => true, 'v' => boleh('inquiry') ? panel_inquiry() : []]);

case 'statistik':
    $tkh = fn($v, $lalai) => preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)($v ?? '')) ? (string)$v : $lalai;
    $hingga = $tkh($in['hingga'] ?? null, date('Y-m-d'));
    $dari = $tkh($in['dari'] ?? null, date('Y-m-d', strtotime($hingga . ' -6 days')));
    if ($dari > $hingga) [$dari, $hingga] = [$hingga, $dari];
    $hari = (int)round((strtotime($hingga) - strtotime($dari)) / 86400) + 1;
    if ($hari > 400) { $dari = date('Y-m-d', strtotime($hingga . ' -399 days')); $hari = 400; }
    $sbl1 = date('Y-m-d', strtotime($dari . ' -1 day')); $sbl0 = date('Y-m-d', strtotime($dari . " -$hari days"));
    $V = "SUM(CASE WHEN jenis = 'view' THEN 1 ELSE 0 END)";
    $K = fn($k) => "SUM(CASE WHEN jenis = 'klik' AND sasaran = '$k' THEN 1 ELSE 0 END)";
    $ringkas = function ($d0, $d1) use ($V, $K) {
        $r = q("SELECT $V AS view, COUNT(DISTINCT CASE WHEN jenis = 'view' THEN pelawat END) AS pelawat, {$K('wa')} AS wa, {$K('hantar_inquiry')} AS inquiry,
            {$K('hantar_itinerari')} AS itinerari, {$K('hantar_hubungi')} AS hubungi FROM statistik WHERE tarikh BETWEEN ? AND ?", [$d0, $d1])->fetch() ?: [];
        return array_map('intval', $r);
    };
    try {
        $mula = q('SELECT MIN(tarikh) FROM statistik')->fetchColumn();
        json_keluar(['ok' => true, 'dari' => $dari, 'hingga' => $hingga, 'mula' => $mula ?: null,
            'kini' => $ringkas($dari, $hingga), 'sebelum' => $ringkas($sbl0, $sbl1),
            'harian' => q("SELECT tarikh, $V AS view, COUNT(DISTINCT CASE WHEN jenis = 'view' THEN pelawat END) AS pelawat FROM statistik WHERE tarikh BETWEEN ? AND ? GROUP BY tarikh ORDER BY tarikh", [$dari, $hingga])->fetchAll(),
            'page' => q("SELECT page, $V AS view, COUNT(DISTINCT CASE WHEN jenis = 'view' THEN pelawat END) AS pelawat, {$K('wa')} AS wa, {$K('inquiry')} AS inquiry,
                {$K('hantar_inquiry')} AS hantar FROM statistik WHERE tarikh BETWEEN ? AND ? GROUP BY page ORDER BY view DESC LIMIT 200", [$dari, $hingga])->fetchAll(),
            'klik' => q("SELECT sasaran AS k, COUNT(*) AS n FROM statistik WHERE jenis = 'klik' AND tarikh BETWEEN ? AND ? GROUP BY sasaran ORDER BY n DESC", [$dari, $hingga])->fetchAll(),
            'peranti' => q("SELECT peranti AS k, COUNT(DISTINCT pelawat) AS n FROM statistik WHERE jenis = 'view' AND tarikh BETWEEN ? AND ? GROUP BY peranti ORDER BY n DESC", [$dari, $hingga])->fetchAll(),
            'sumber' => q("SELECT sumber AS k, COUNT(*) AS n FROM statistik WHERE jenis = 'view' AND sumber <> 'dalaman' AND tarikh BETWEEN ? AND ? GROUP BY sumber ORDER BY n DESC LIMIT 12", [$dari, $hingga])->fetchAll()]);
    } catch (Throwable $e) {
        json_keluar(['ok' => true, 'dari' => $dari, 'hingga' => $hingga, 'mula' => null, 'kini' => [], 'sebelum' => [], 'harian' => [], 'page' => [], 'klik' => [], 'peranti' => [], 'sumber' => []]);
    }

case 'tiktok':   // buka pautan pendek TikTok → pautan penuh
    $penuh = tiktok_buka((string)($in['u'] ?? ''));
    json_keluar($penuh ? ['ok' => true, 'u' => $penuh] : ['ok' => false, 'err' => 'Tidak dapat membuka pautan pendek TikTok. Buka video di browser dan salin pautan penuh (tiktok.com/@akaun/video/…).']);

case 'upload':
    if (!pentadbir() && !array_diff(pengguna()['akses'] ?? [], ['inquiry', 'inquiry_urus'])) json_keluar(['ok' => false, 'err' => 'Tiada kebenaran memuat naik gambar.'], 403);
    $f = $_FILES['f'] ?? null;
    if (!$f || $f['error'] !== UPLOAD_ERR_OK) json_keluar(['ok' => false, 'err' => 'Muat naik gagal.'], 400);
    if ($f['size'] > 4 * 1048576) json_keluar(['ok' => false, 'err' => 'Gambar melebihi 4 MB.'], 400);
    $info = @getimagesize($f['tmp_name']);
    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'][$info['mime'] ?? ''] ?? null;
    if (!$ext) json_keluar(['ok' => false, 'err' => 'Hanya JPG, PNG, WebP atau GIF.'], 400);
    $dir = 'uploads/' . date('Ym');
    if (!is_dir(ROOT . '/' . $dir) && !mkdir(ROOT . '/' . $dir, 0755, true)) json_keluar(['ok' => false, 'err' => 'Folder uploads tidak boleh dicipta.'], 500);
    $nama = $dir . '/' . date('d') . '-' . bin2hex(random_bytes(6)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], ROOT . '/' . $nama)) json_keluar(['ok' => false, 'err' => 'Gagal menyimpan gambar.'], 500);
    json_keluar(['ok' => true, 'path' => $nama]);

case 'eksport':
    header('Content-Disposition: attachment; filename="sandaran-jjt-' . date('Y-m-d') . '.json"');
    $S = panel_state(); unset($S['users']);
    json_keluar($S);

case 'pass':   // tukar kata laluan sendiri
    $u = q('SELECT hash FROM pengguna WHERE id = ?', [pengguna()['id']])->fetch();
    if (!password_verify((string)($in['lama'] ?? ''), (string)$u['hash'])) json_keluar(['ok' => false, 'err' => 'Kata laluan semasa salah.'], 400);
    if (strlen((string)($in['baru'] ?? '')) < 8) json_keluar(['ok' => false, 'err' => 'Kata laluan baharu sekurang-kurangnya 8 aksara.'], 422);
    if (hash_equals((string)$in['lama'], (string)$in['baru'])) json_keluar(['ok' => false, 'err' => 'Kata laluan baharu mesti berbeza daripada kata laluan semasa.'], 422);
    q('UPDATE pengguna SET hash = ?, wajib_tukar = 0 WHERE id = ?', [password_hash((string)$in['baru'], PASSWORD_DEFAULT), pengguna()['id']]);
    $_SESSION['pengguna']['wajib'] = false;
    json_keluar(['ok' => true]);

case 'pengguna':   // Pentadbir sahaja: tambah / peranan / buang / set semula kata laluan
    if (!pentadbir()) json_keluar(['ok' => false, 'err' => 'Untuk Pentadbir sahaja.'], 403);
    $op = $in['op'] ?? ''; $id = (int)($in['id'] ?? 0); $sementara = null;
    $admins = fn() => (int)q("SELECT COUNT(*) FROM pengguna WHERE peranan = 'Pentadbir'")->fetchColumn();
    if ($op === 'tambah') {
        $emel = strtolower(trim((string)($in['emel'] ?? ''))); $nama = $str($in['nama'] ?? '', 160) ?: $emel;
        if (!filter_var($emel, FILTER_VALIDATE_EMAIL)) json_keluar(['ok' => false, 'err' => 'E-mel tidak sah.'], 422);
        if (q('SELECT COUNT(*) FROM pengguna WHERE LOWER(emel) = ?', [$emel])->fetchColumn()) json_keluar(['ok' => false, 'err' => 'E-mel sudah didaftarkan.'], 422);
        $sementara = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(9))), 0, 10);
        q('INSERT INTO pengguna (nama, emel, hash, peranan, dicipta, wajib_tukar) VALUES (?, ?, ?, ?, ?, 1)', [$nama, $emel, password_hash($sementara, PASSWORD_DEFAULT), 'Staf', $kini]);
    } elseif ($op === 'peranan') {
        $p = ($in['peranan'] ?? '') === 'Pentadbir' ? 'Pentadbir' : 'Staf';
        $cur = q('SELECT peranan FROM pengguna WHERE id = ?', [$id])->fetchColumn();
        if ($cur === 'Pentadbir' && $p === 'Staf' && $admins() < 2) json_keluar(['ok' => false, 'err' => 'Mesti ada sekurang-kurangnya 1 Pentadbir.'], 422);
        q('UPDATE pengguna SET peranan = ? WHERE id = ?', [$p, $id]);
    } elseif ($op === 'buang') {
        if ($id === (int)pengguna()['id']) json_keluar(['ok' => false, 'err' => 'Anda tidak boleh membuang akaun sendiri.'], 422);
        $cur = q('SELECT peranan FROM pengguna WHERE id = ?', [$id])->fetchColumn();
        if ($cur === 'Pentadbir' && $admins() < 2) json_keluar(['ok' => false, 'err' => 'Mesti ada sekurang-kurangnya 1 Pentadbir.'], 422);
        q('DELETE FROM pengguna WHERE id = ?', [$id]);
    } elseif ($op === 'akses') {
        $a = array_values(array_intersect(AKSES_SEMUA, array_map('strval', (array)($in['akses'] ?? []))));
        if (in_array('inquiry_urus', $a, true) && !in_array('inquiry', $a, true)) $a[] = 'inquiry';
        q('UPDATE pengguna SET akses = ? WHERE id = ?', [json_encode($a), $id]);
    } elseif ($op === 'reset') {
        $sementara = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(9))), 0, 10);
        q('UPDATE pengguna SET hash = ?, wajib_tukar = 1 WHERE id = ?', [password_hash($sementara, PASSWORD_DEFAULT), $id]);
    } else json_keluar(['ok' => false, 'err' => 'Operasi tidak dikenali.'], 400);
    json_keluar(['ok' => true, 'v' => panel_users(), 'sementara' => $sementara]);

case 'save':
    $k = (string)($in['k'] ?? ''); $v = $in['v'] ?? null;
    $semua = array_merge(['pakej', 'jenis', 'kawasan', 'dest', 'inquiry', 'testi', 'blog', 'tetapan'], KANDUNGAN_KEYS);
    if (!in_array($k, $semua, true)) json_keluar(['ok' => false, 'err' => 'Data tidak dikenali.'], 400);
    if (!pentadbir() && (!isset(KUNCI_AKSES[$k]) || !boleh(KUNCI_AKSES[$k]))) json_keluar(['ok' => false, 'err' => 'Anda tiada kebenaran untuk bahagian ini. Hubungi Pentadbir.'], 403);
    if (!is_array($v)) json_keluar(['ok' => false, 'err' => 'Data tidak sah.'], 400);
    $db = db();
    $db->beginTransaction();
    try {
        $ins = function (string $t, array $row) use ($db) {
            $c = array_keys($row);
            $db->prepare("INSERT INTO $t (" . implode(',', $c) . ') VALUES (' . implode(',', array_fill(0, count($c), '?')) . ')')->execute(array_values($row));
        };
        $list = fn($x) => array_values(array_filter(array_map(fn($s) => $str($s, 500), (array)($x ?? [])), fn($s) => $s !== ''));
        switch ($k) {
        case 'pakej':
            $db->exec('DELETE FROM pakej');
            foreach (array_values($v) as $i => $p) {
                if (!($id = $slug($p['id'] ?? ''))) continue;
                $tarikh = is_array($p['tarikh'] ?? null) ? array_values(array_map(fn($d) => ['t' => $str($d['t'] ?? '', 10), 'k' => max(0, (int)($d['k'] ?? 0)),
                    's' => (int)($d['k'] ?? 0) <= 0 ? 'penuh' : ((int)$d['k'] <= 5 ? 'hampir' : 'tersedia')], $p['tarikh'])) : 'fleksibel';
                $itin = array_values(array_map(fn($d) => ['tajuk' => $str($d['tajuk'] ?? '', 200), 'aktiviti' => $list($d['aktiviti'] ?? []), 'makan' => $str($d['makan'] ?? '', 10)], (array)($p['itinerari'] ?? [])));
                $ins('pakej', ['id' => $id, 'dest' => $str($p['dest'] ?? '', 120), 'jenis' => $str($p['jenis'] ?? '', 120), 'nama' => $str($p['nama'] ?? '', 200),
                    'hari' => (int)($p['hari'] ?? 1), 'malam' => (int)($p['malam'] ?? 0), 'harga' => (int)($p['harga'] ?? 0), 'harga_asal' => max(0, (int)($p['hargaAsal'] ?? 0)) ?: null, 'hotel' => $str($p['hotel'] ?? '', 160),
                    'kumpulan' => $str($p['kumpulan'] ?? '', 120), 'badge' => $str($p['badge'] ?? '', 60) ?: null, 'laluan' => $jsn($list($p['laluan'] ?? [])), 'tarikh' => $jsn($tarikh),
                    'sorotan' => $jsn($list($p['sorotan'] ?? [])), 'itinerari' => $jsn($itin), 'termasuk' => $jsn($list($p['termasuk'] ?? [])), 'tidak' => $jsn($list($p['tidak'] ?? [])),
                    'nota' => $jsn($list($p['nota'] ?? [])), 'susunan' => $i, 'aktif' => !empty($p['aktif']) ? 1 : 0, 'dikemaskini' => date('Y-m-d H:i:s'), 'img' => $gambar($p['gambar'] ?? null),
                    'harga_detail' => ($hd = $hargaDetail($p['hargaDetail'] ?? null)) ? $jsn($hd) : null, 'tarikan' => $jsn($tarikan($p['tarikan'] ?? [])), 'cuaca' => $jsn($cuaca($p['cuaca'] ?? []))]);
            }
            break;
        case 'dest':
            $db->exec('DELETE FROM destinasi');
            foreach (array_values($v) as $i => $d) {
                if (!($id = $slug($d['id'] ?? ''))) continue;
                $ins('destinasi', ['id' => $id, 'nama' => $str($d['nama'] ?? '', 200), 'pendek' => $str($d['pendek'] ?? '', 120) ?: null, 'kawasan' => $str($d['kawasan'] ?? '', 120),
                    'img' => $gambar($d['img'] ?? null), 'musim' => $str($d['musim'] ?? '', 160), 'matawang' => $str($d['matawang'] ?? '', 120), 'terbang' => $str($d['terbang'] ?? '', 120),
                    'visa' => $str($d['visa'] ?? '', 160), 'intro' => $str($d['intro'] ?? '', 2000), 'susunan' => isset($d['urutan']) ? (int)$d['urutan'] : $i, 'aktif' => 1, 'papar_utama' => !empty($d['papar']) ? 1 : 0]);
            }
            break;
        case 'jenis':
            $db->exec('DELETE FROM jenis');
            foreach (array_values($v) as $i => $x) if ($id = $slug($x['id'] ?? '')) $ins('jenis', ['id' => $id, 'nama' => $str($x['nama'] ?? '', 120), 'ikon' => $str($x['ikon'] ?? 't-all', 40), 'ringkas' => $str($x['ringkas'] ?? '', 300), 'susunan' => $i]);
            break;
        case 'kawasan':
            $db->exec('DELETE FROM kawasan');
            foreach (array_values($v) as $i => $x) if ($id = $slug($x['id'] ?? '')) $ins('kawasan', ['id' => $id, 'nama' => $str($x['nama'] ?? '', 120), 'susunan' => $i]);
            break;
        case 'testi':
            $db->exec('DELETE FROM testimoni');
            foreach (array_values($v) as $i => $x) $ins('testimoni', ['nama' => $str($x['n'] ?? '', 160), 'pakej' => $str($x['p'] ?? '', 160), 'img' => $gambar($x['img'] ?? null),
                'teks' => $str($x['q'] ?? '', 3000), 'susunan' => $i, 'aktif' => !empty($x['papar']) ? 1 : 0, 'sumber' => $str($x['sumber'] ?? '', 60),
                'gambar' => $jsn(array_values(array_filter(array_map($gambar, array_slice((array)($x['g'] ?? []), 0, 6)))))]);
            break;
        case 'blog':
            $db->exec('DELETE FROM blog');
            foreach (array_values($v) as $x) if ($id = $slug($x['id'] ?? '')) $ins('blog', ['id' => $id, 'tajuk' => $str($x['tajuk'] ?? '', 255), 'kat' => $str($x['kat'] ?? '', 120),
                'img' => $gambar($x['img'] ?? null), 'tarikh' => $str($x['tarikh'] ?? date('Y-m-d'), 10), 'minit' => max(1, (int)($x['minit'] ?? 3)), 'dest' => $str($x['dest'] ?? '', 120) ?: null,
                'ringkas' => $str($x['ringkas'] ?? '', 400), 'meta' => $str($x['meta'] ?? '', 300), 'isi' => html_selamat((string)($x['isi'] ?? '')), 'aktif' => !empty($x['terbit']) ? 1 : 0]);
            break;
        case 'inquiry':   // kemas kini status & catatan sahaja; inquiry baharu datang dari website
            $st = $db->prepare('UPDATE inquiry SET status = ?, catatan = ? WHERE id = ?');
            foreach ((array)$v as $x) {
                $s = in_array($x['status'] ?? '', ['Baru', 'Dihubungi', 'Tempahan', 'Tutup'], true) ? $x['status'] : 'Baru';
                $st->execute([$s, $str($x['catatan'] ?? '', 3000), (int)($x['id'] ?? 0)]);
            }
            foreach ((array)($in['hapus'] ?? []) as $id) $db->prepare('DELETE FROM inquiry WHERE id = ?')->execute([(int)$id]);
            break;
        case 'tetapan':
            $peta = ['wa' => 'wa', 'emel' => 'emel', 'alamat' => 'alamat', 'waktu' => 'waktu', 'notif' => 'notif', 'fb' => 'fb', 'ig' => 'ig', 'tiktok' => 'tt', 'site' => 'site_url', 'pratonton' => 'pratonton', 'wa_teks' => 'wa_teks', 'ga4' => 'ga4'];
            foreach ($peta as $dari => $ke) {
                if (!array_key_exists($dari, $v)) continue;
                $val = $str($v[$dari], 1000);
                if ($dari === 'wa') $val = preg_replace('/\D/', '', $val);
                if ($dari === 'site') $val = rtrim($val, '/');
                if ($dari === 'ga4') { $val = strtoupper(preg_replace('/\s+/', '', $val)); if ($val !== '' && !preg_match('/^G-[A-Z0-9]{4,16}$/', $val)) json_keluar(['ok' => false, 'err' => 'ID Google Analytics tidak sah. Format: G-XXXXXXXXXX'], 422); }
                $db->prepare('DELETE FROM tetapan WHERE k = ?')->execute([$ke]);
                $db->prepare('INSERT INTO tetapan (k, v) VALUES (?, ?)')->execute([$ke, $val]);
            }
            break;
        default:   // kandungan halaman (JSON)
            if ($k === 'halaman') foreach ($v as $hk => $hv) if (is_array($hv) && isset($hv['html'])) $v[$hk]['html'] = html_selamat((string)$hv['html']);
            if ($k === 'utama' && isset($v['hero'])) $v['hero'] = $gambar($v['hero']) ?? 'images/hero.jpg';
            if ($k === 'banner') foreach ($v as $bi => $bv) $v[$bi]['img'] = $gambar($bv['img'] ?? null);
            if ($k === 'shorts') $v = array_slice(array_values(array_filter(array_map(function ($x) use ($str, $gambar) {   // video YouTube / TikTok
                $x = is_array($x) ? $x : ['u' => $x];
                $vi = video_info((string)($x['u'] ?? ''));
                return $vi ? ['u' => $vi['u'], 't' => $str($x['t'] ?? '', 120), 'c' => $gambar($x['c'] ?? null) ?? ''] : null;
            }, (array)$v))), 0, 10);
            if ($k === 'tentangX' && is_array($v)) {
                foreach (['hero_img', 'foto1', 'foto2', 'pasukan_img', 'cta_img', 'lesen_url'] as $gk) if (array_key_exists($gk, $v)) $v[$gk] = $gambar($v[$gk]) ?? '';
                if (isset($v['galeri'])) $v['galeri'] = array_values(array_filter(array_map($gambar, array_slice((array)$v['galeri'], 0, 8))));
            }
            $db->prepare('DELETE FROM kandungan WHERE k = ?')->execute([$k]);
            $db->prepare('INSERT INTO kandungan (k, v) VALUES (?, ?)')->execute([$k, $jsn($v)]);
        }
        $db->commit();
    } catch (Throwable $e) {
        $db->rollBack();
        error_log('JJT panel save ' . $k . ': ' . $e->getMessage());
        json_keluar(['ok' => false, 'err' => 'Gagal menyimpan. Sila cuba lagi.'], 500);
    }
    json_keluar(['ok' => true]);

default:
    json_keluar(['ok' => false, 'err' => 'Tindakan tidak dikenali.'], 400);
}
