<?php
/* Penjejak ringkas: rekod view page & klik butang untuk menu Statistik di dashboard.
   Tiada cookie, tiada IP disimpan — pelawat dikenal pasti dengan kod harian tanpa nama. */
require dirname(__DIR__) . '/inc/db.php';
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex');
http_response_code(204);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$ua = (string)($_SERVER['HTTP_USER_AGENT'] ?? '');
if ($ua === '' || preg_match('~bot|crawl|spider|slurp|facebookexternalhit|whatsapp|preview|headless|lighthouse|pingdom|uptime|monitor|curl|wget|python|java/|httpclient~i', $ua)) exit;

/* Staf yang sedang log masuk ke dashboard tidak dikira */
if (!empty($_COOKIE['jjtpanel'])) {
    session_name('jjtpanel');
    @session_start(['read_and_close' => true]);
    if (!empty($_SESSION['pengguna'])) exit;
}

$jenis = ($_POST['t'] ?? '') === 'klik' ? 'klik' : 'view';
$page = mb_substr(trim((string)($_POST['p'] ?? '/')), 0, 190);
if ($page === '' || $page[0] !== '/') $page = '/' . $page;
$page = preg_replace('~[^\w/?=&.\-]~u', '', $page) ?: '/';
$sasaran = $jenis === 'klik' ? preg_replace('/[^a-z_]/', '', strtolower((string)($_POST['k'] ?? ''))) : '';
if ($jenis === 'klik' && $sasaran === '') exit;
$sasaran = substr($sasaran, 0, 40);

$hari = date('Y-m-d');
$garam = tetapan('pratonton', 'jjt');
$pelawat = substr(hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . '|' . $ua . '|' . $hari . '|' . $garam), 0, 16);
$peranti = preg_match('~ipad|tablet|(android(?!.*mobile))~i', $ua) ? 'Tablet' : (preg_match('~mobi|iphone|android~i', $ua) ? 'Telefon' : 'Desktop');

/* Sumber pelawat (untuk view sahaja) */
$sumber = '';
if ($jenis === 'view') {
    $utm = strtolower(trim((string)($_POST['u'] ?? '')));
    $ref = strtolower((string)parse_url((string)($_POST['r'] ?? ''), PHP_URL_HOST));
    $sendiri = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
    $sendiri = preg_replace(['/:\d+$/', '/^www\./'], '', $sendiri);
    $peta = ['~google\.~' => 'Google', '~(^|\.)(facebook\.com|fb\.com|fb\.me)$~' => 'Facebook', '~(^|\.)instagram\.com$~' => 'Instagram', '~(^|\.)tiktok\.com$~' => 'TikTok',
        '~(^|\.)(whatsapp\.com|wa\.me)$~' => 'WhatsApp', '~(^|\.)(youtube\.com|youtu\.be)$~' => 'YouTube', '~(^|\.)bing\.com$~' => 'Bing', '~yahoo\.~' => 'Yahoo',
        '~(^|\.)(t\.co|x\.com|twitter\.com)$~' => 'X / Twitter', '~(^|\.)(t\.me|telegram\.org)$~' => 'Telegram', '~(^|\.)threads\.net$~' => 'Threads', '~(^|\.)linkedin\.com$~' => 'LinkedIn'];
    $utmPeta = ['~google|gads|adwords~' => 'Google', '~facebook|^fb|meta~' => 'Facebook', '~instagram|^ig~' => 'Instagram', '~tiktok~' => 'TikTok', '~whatsapp|^wa$~' => 'WhatsApp', '~youtube|^yt~' => 'YouTube'];
    if ($utm !== '') {
        foreach ($utmPeta as $re => $v) if (preg_match($re, $utm)) { $sumber = $v; break; }
        if ($sumber === '') $sumber = mb_substr(ucfirst(preg_replace('/[^\w .-]/u', '', $utm)), 0, 40);
    } elseif ($ref === '') $sumber = 'Terus';
    else {
        $ref = preg_replace('/^www\./', '', $ref);
        if ($sendiri !== '' && ($ref === $sendiri || str_ends_with($ref, '.' . $sendiri))) $sumber = 'dalaman';
        else {
            foreach ($peta as $re => $v) if (preg_match($re, $ref)) { $sumber = $v; break; }
            if ($sumber === '') $sumber = mb_substr($ref, 0, 40);
        }
    }
}

$baris = [date('Y-m-d H:i:s'), $hari, $jenis, $page, $sasaran, $pelawat, $peranti, $sumber];
try {
    if ((int)q('SELECT COUNT(*) FROM statistik WHERE tarikh = ? AND pelawat = ?', [$hari, $pelawat])->fetchColumn() >= 300) exit;   // had harian setiap pelawat
    q('INSERT INTO statistik (masa, tarikh, jenis, page, sasaran, pelawat, peranti, sumber) VALUES (?,?,?,?,?,?,?,?)', $baris);
} catch (Throwable $e) {   // jadual belum wujud: cipta dan cuba sekali lagi
    try {
        require_once dirname(__DIR__) . '/inc/schema.php';
        db()->exec(jjt_sql_statistik(($CONFIG['driver'] ?? 'mysql') === 'sqlite' ? 'sqlite' : 'mysql'));
        q('INSERT INTO statistik (masa, tarikh, jenis, page, sasaran, pelawat, peranti, sumber) VALUES (?,?,?,?,?,?,?,?)', $baris);
    } catch (Throwable $e2) { error_log('JJT statistik: ' . $e2->getMessage()); }
}
if (random_int(1, 500) === 1) { try { q('DELETE FROM statistik WHERE tarikh < ?', [date('Y-m-d', strtotime('-400 days'))]); } catch (Throwable $e) {} }   // simpan ~13 bulan
