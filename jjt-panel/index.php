<?php
/* Dashboard JJT — jalantravel.com/jjt-panel */
require __DIR__ . '/inc.php';
panel_migrasi();
header('Cache-Control: no-store');

$setup = (int)q('SELECT COUNT(*) FROM pengguna')->fetchColumn() === 0;
$u = pengguna();
if ($u) {   // segarkan peranan & akses dari database (mungkin diubah oleh Pentadbir)
    $r = q('SELECT id, nama, emel, peranan, akses, wajib_tukar FROM pengguna WHERE id = ?', [$u['id']])->fetch();
    if (!$r) { $_SESSION = []; $u = null; }
    else $u = $_SESSION['pengguna'] = ['id' => (int)$r['id'], 'nama' => $r['nama'], 'emel' => $r['emel'], 'peranan' => $r['peranan'], 'akses' => $r['peranan'] === 'Pentadbir' ? AKSES_SEMUA : akses_dari($r['akses'] ?? null), 'wajib' => (bool)(int)($r['wajib_tukar'] ?? 0)];
}
$P = ['base' => BASE, 'csrf' => $_SESSION['csrf'], 'user' => $u, 'setup' => $setup, 'state' => $u ? panel_state() : null];
$html = file_get_contents(__DIR__ . '/panel.html');
$html = str_replace('__BASE__', htmlspecialchars(BASE, ENT_QUOTES), $html);
$html = str_replace('__PANEL__', json_encode($P, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP), $html);
echo $html;
