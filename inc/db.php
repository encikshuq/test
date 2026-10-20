<?php
/* Sambungan database + pembantu asas. Semua page memanggil fail ini. */
declare(strict_types=1);
mb_internal_encoding('UTF-8');
date_default_timezone_set('Asia/Kuala_Lumpur');

define('ROOT', dirname(__DIR__));
if (!is_file(ROOT . '/config.php')) {
    $d = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    $d = preg_replace('~/(api|jjt-panel)$~', '', $d);
    header('Location: ' . $d . '/install.php');
    exit;
}
$CONFIG = require ROOT . '/config.php';
define('BASE', rtrim($CONFIG['base'] ?? '/', '/') . '/');

function db(): PDO
{
    static $pdo = null;
    global $CONFIG;
    if ($pdo) return $pdo;
    try {
        if (($CONFIG['driver'] ?? 'mysql') === 'sqlite') {
            $pdo = new PDO('sqlite:' . $CONFIG['sqlite']);
        } else {
            $dsn = "mysql:host={$CONFIG['host']};dbname={$CONFIG['name']};charset=utf8mb4";
            $pdo = new PDO($dsn, $CONFIG['user'], $CONFIG['pass']);
        }
    } catch (PDOException $e) {
        http_response_code(503);
        exit('<h1>Laman sedang diselenggara</h1><p>Sila cuba sebentar lagi.</p>');
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
}

function q(string $sql, array $args = []): PDOStatement
{
    $st = db()->prepare($sql);
    $st->execute($args);
    return $st;
}

/* Tetapan (nombor WhatsApp, emel, media sosial, URL laman) */
function tetapan(string $k, string $lalai = ''): string
{
    static $T = null;
    if ($T === null) {
        $T = [];
        foreach (q('SELECT k, v FROM tetapan')->fetchAll() as $r) $T[$r['k']] = (string)$r['v'];
    }
    if ($k === 'wa_papar') return wa_papar($T['wa'] ?? '');
    if ($k === 'tt' && !isset($T['tt'])) return $T['tiktok'] ?? $lalai;
    return $T[$k] ?? $lalai;
}

/* Paparan nombor WhatsApp: 60123316486 → 012-331 6486 */
function wa_papar(string $wa): string
{
    $d = preg_replace('/\D/', '', $wa);
    if (str_starts_with($d, '60')) $d = '0' . substr($d, 2);
    if (strlen($d) === 10) return substr($d, 0, 3) . '-' . substr($d, 3, 3) . ' ' . substr($d, 6);
    if (strlen($d) === 11) return substr($d, 0, 3) . '-' . substr($d, 3, 4) . ' ' . substr($d, 7);
    return $d ?: $wa;
}

