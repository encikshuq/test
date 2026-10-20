<?php
/* Baca kandungan dari database dalam bentuk yang sama seperti data.js asal. */

function _json_cols(array $r, array $cols): array
{
    foreach ($cols as $c) {
        if (!array_key_exists($c, $r)) continue;
        $v = $r[$c];
        $r[$c] = ($v === null || $v === '') ? null : json_decode((string)$v, true);
    }
    return $r;
}

function semua_jenis(): array
{
    static $x = null;
    return $x ??= q('SELECT * FROM jenis ORDER BY susunan, id')->fetchAll();
}
function jenis(?string $id): ?array
{
    foreach (semua_jenis() as $j) if ($j['id'] === $id) return $j;
    return null;
}

function semua_kawasan(): array
{
    static $x = null;
    return $x ??= q('SELECT * FROM kawasan ORDER BY susunan, id')->fetchAll();
}

function semua_destinasi(): array
{
    static $x = null;
    return $x ??= q('SELECT * FROM destinasi WHERE aktif = 1 ORDER BY susunan, id')->fetchAll();
}
function dest(?string $id): ?array
{
    foreach (semua_destinasi() as $d) if ($d['id'] === $id) return $d;
    return null;
}
function nama_pendek(array $d): string
{
    return ($d['pendek'] ?? '') !== '' ? $d['pendek'] : $d['nama'];
}

function semua_pakej(): array
{
    static $x = null;
    if ($x !== null) return $x;
    $aktifDest = array_column(semua_destinasi(), 'id');
    $x = [];
    $rows = q('SELECT * FROM pakej WHERE aktif = 1 ORDER BY susunan, id')->fetchAll();
    $perlu = $rows && !array_key_exists('harga_asal', $rows[0]);
    if (!$perlu && $rows) { try { $perlu = !q("SELECT COUNT(*) FROM tetapan WHERE k = 'pakej_sapporo'")->fetchColumn(); } catch (Throwable $e) {} }
    if ($perlu) {   // database belum dikemas kini / contoh belum diisi: jalankan sekali
        try {
            global $CONFIG;
            require_once ROOT . '/inc/schema.php';
            jjt_migrasi(db(), ($CONFIG['driver'] ?? 'mysql') === 'sqlite' ? 'sqlite' : 'mysql');
            $rows = q('SELECT * FROM pakej WHERE aktif = 1 ORDER BY susunan, id')->fetchAll();
        } catch (Throwable $e) { error_log('JJT migrasi: ' . $e->getMessage()); }
    }
    foreach ($rows as $r) {
        if (!in_array($r['dest'], $aktifDest, true) || !jenis($r['jenis'])) continue;
        $x[] = _pakej_baris($r);
    }
    return $x;
}
function _pakej_baris(array $r): array
{
    $r = _json_cols($r, ['laluan', 'tarikh', 'sorotan', 'itinerari', 'termasuk', 'tidak', 'nota', 'harga_detail', 'tarikan', 'cuaca']);
    foreach (['tarikan', 'cuaca'] as $c) $r[$c] = is_array($r[$c] ?? null) ? $r[$c] : [];
    $r['harga_detail'] = is_array($r['harga_detail'] ?? null) ? $r['harga_detail'] : null;
    foreach (['laluan', 'sorotan', 'itinerari', 'termasuk', 'tidak', 'nota'] as $c) $r[$c] = $r[$c] ?? [];
    $r['hari'] = (int)$r['hari'];
    $r['malam'] = (int)$r['malam'];
    $r['harga'] = (int)$r['harga'];
    $r['harga_asal'] = (int)($r['harga_asal'] ?? 0) > $r['harga'] ? (int)$r['harga_asal'] : 0;   // harga sebelum promo (dipotong)
    $r['img'] = $r['img'] ?? null;
    return $r;
}
/* Pakej tersembunyi: hanya untuk pratonton pentadbir */
function pakej_draf(string $id): ?array
{
    if (!function_exists('admin_log_masuk') || !admin_log_masuk()) return null;
    $r = q('SELECT * FROM pakej WHERE id = ?', [$id])->fetch();
    return $r && dest($r['dest']) && jenis($r['jenis']) ? _pakej_baris($r) : null;
}
/* Gambar pakej: gambar khas pakej, atau gambar destinasi */
function gambar_pakej(array $p): ?string
{
    return $p['img'] ?: (dest($p['dest'])['img'] ?? null);
}
function pakej(?string $id): ?array
{
    foreach (semua_pakej() as $p) if ($p['id'] === $id) return $p;
    return null;
}
function pakej_dest(string $destId): array
{
    return array_values(array_filter(semua_pakej(), fn($p) => $p['dest'] === $destId));
}
function tarikh_tetap(array $p): bool
{
    return is_array($p['tarikh']);
}

function semua_testimoni(): array
{
    static $x = null;
    return $x ??= array_map(function ($r) {   // gambar = senarai gambar review (screenshot / gambar trip)
        $r['gambar'] = array_values(array_filter((array)(json_decode((string)($r['gambar'] ?? ''), true) ?: []), 'is_string'));
        return $r;
    }, q('SELECT * FROM testimoni WHERE aktif = 1 ORDER BY susunan, id')->fetchAll());
}

function semua_blog(): array
{
    static $x = null;
    return $x ??= q('SELECT id, tajuk, kat, img, tarikh, minit, dest, ringkas, meta FROM blog WHERE aktif = 1 ORDER BY tarikh DESC, id')->fetchAll();
}
function blog(?string $id): ?array
{
    $r = q('SELECT * FROM blog WHERE aktif = 1 AND id = ?', [(string)$id])->fetch();
    if (!$r && function_exists('admin_log_masuk') && admin_log_masuk()) $r = q('SELECT * FROM blog WHERE id = ?', [(string)$id])->fetch();  // draf: pratonton pentadbir
    return $r ?: null;
}

/* Data ringkas untuk JavaScript (borang inquiry, penapis main page) */
function data_js(bool $penuh = true): string
{
    $pk = array_map(function ($p) use ($penuh) {
        $o = ['id' => $p['id'], 'dest' => $p['dest'], 'jenis' => $p['jenis'], 'nama' => $p['nama'], 'hari' => $p['hari'], 'malam' => $p['malam'], 'harga' => $p['harga'], 'img' => $p['img'] ? img($p['img']) : null, 'laluan' => $p['laluan'], 'asal' => $p['harga_asal'] ?? 0];
        if ($penuh || $p['jenis'] === 'series') $o['tarikh'] = $p['tarikh'];   // jadual Series Departure di main page
        return $o;
    }, semua_pakej());
    $ds = array_map(fn($d) => ['id' => $d['id'], 'nama' => $d['nama'], 'pendek' => $d['pendek'] ?: null, 'img' => $d['img'] ? img($d['img']) : null, 'utama' => (int)($d['papar_utama'] ?? 1) === 1], semua_destinasi());
    $js = array_map(fn($j) => ['id' => $j['id'], 'nama' => $j['nama'], 'ikon' => $j['ikon'], 'ringkas' => $j['ringkas']], semua_jenis());
    $o = ['WA' => tetapan('wa'), 'WA_PAPAR' => tetapan('wa_papar'), 'BASE' => BASE, 'JENIS' => $js, 'DESTINASI' => $ds, 'PAKEJ' => $pk, 'PILIHAN' => PILIHAN_PAKEJ];
    if ($penuh && function_exists('kand')) { $b = kand('borang'); $o['BILIK'] = $b['bilik']; $o['NEGERI'] = $b['negeri']; $o['PERKARA'] = $b['perkara']; }
    return json_encode($o, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);
}
