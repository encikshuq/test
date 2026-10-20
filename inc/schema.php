<?php
/* Struktur jadual database JJT + import data awal (seed.json) */

function jjt_schema(string $driver): array
{
    $my = $driver === 'mysql';
    $ai = $my ? 'INT AUTO_INCREMENT PRIMARY KEY' : 'INTEGER PRIMARY KEY AUTOINCREMENT';
    $tail = $my ? ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci' : '';
    $long = $my ? 'LONGTEXT' : 'TEXT';
    $k = 'VARCHAR(120)';
    return [
        "CREATE TABLE IF NOT EXISTS tetapan (k VARCHAR(60) PRIMARY KEY, v TEXT)$tail",
        "CREATE TABLE IF NOT EXISTS jenis (id $k PRIMARY KEY, nama VARCHAR(120) NOT NULL, ikon VARCHAR(40), ringkas TEXT, susunan INT DEFAULT 0)$tail",
        "CREATE TABLE IF NOT EXISTS kawasan (id $k PRIMARY KEY, nama VARCHAR(120) NOT NULL, susunan INT DEFAULT 0)$tail",
        "CREATE TABLE IF NOT EXISTS destinasi (id $k PRIMARY KEY, nama VARCHAR(200) NOT NULL, pendek VARCHAR(120), kawasan VARCHAR(120), img VARCHAR(255),
            musim VARCHAR(160), matawang VARCHAR(120), terbang VARCHAR(120), visa VARCHAR(160), intro TEXT, susunan INT DEFAULT 0, aktif TINYINT DEFAULT 1, papar_utama TINYINT DEFAULT 1)$tail",
        "CREATE TABLE IF NOT EXISTS pakej (id $k PRIMARY KEY, dest VARCHAR(120) NOT NULL, jenis VARCHAR(120) NOT NULL, nama VARCHAR(200) NOT NULL,
            hari INT, malam INT, harga INT, harga_asal INT, hotel VARCHAR(160), kumpulan VARCHAR(120), badge VARCHAR(60),
            laluan TEXT, tarikh TEXT, sorotan TEXT, itinerari $long, termasuk TEXT, tidak TEXT, nota TEXT,
            susunan INT DEFAULT 0, aktif TINYINT DEFAULT 1, dikemaskini VARCHAR(20), img VARCHAR(255), harga_detail TEXT, tarikan TEXT, cuaca TEXT)$tail",
        "CREATE TABLE IF NOT EXISTS testimoni (id $ai, nama VARCHAR(160) NOT NULL, pakej VARCHAR(160), img VARCHAR(255), teks TEXT, susunan INT DEFAULT 0, aktif TINYINT DEFAULT 1, sumber VARCHAR(60), gambar TEXT)$tail",
        "CREATE TABLE IF NOT EXISTS blog (id $k PRIMARY KEY, tajuk VARCHAR(255) NOT NULL, kat VARCHAR(120), img VARCHAR(255), tarikh VARCHAR(10),
            minit INT DEFAULT 3, dest VARCHAR(120), ringkas TEXT, meta TEXT, isi $long, aktif TINYINT DEFAULT 1)$tail",
        "CREATE TABLE IF NOT EXISTS inquiry (id $ai, dicipta VARCHAR(20), jenis VARCHAR(20) DEFAULT 'pakej', pakej_id VARCHAR(120),
            nama VARCHAR(160), telefon VARCHAR(40), emel VARCHAR(160), negeri VARCHAR(60), tarikh VARCHAR(40), peserta VARCHAR(120), bilik VARCHAR(40),
            nota TEXT, mesej TEXT, status VARCHAR(20) DEFAULT 'baru', ip VARCHAR(64), pax TEXT, anggaran INT, setuju TINYINT, perkara VARCHAR(160), catatan TEXT, pilihan VARCHAR(80))$tail",
        "CREATE TABLE IF NOT EXISTS kandungan (k VARCHAR(60) PRIMARY KEY, v $long)$tail",
        "CREATE TABLE IF NOT EXISTS pengguna (id $ai, nama VARCHAR(160), emel VARCHAR(190) UNIQUE, hash VARCHAR(255), peranan VARCHAR(20) DEFAULT 'Staf', akhir VARCHAR(20), dicipta VARCHAR(20), akses TEXT, wajib_tukar TINYINT DEFAULT 0)$tail",
        "CREATE TABLE IF NOT EXISTS log_masuk (id $ai, ip VARCHAR(64), emel VARCHAR(190), masa VARCHAR(20), berjaya TINYINT DEFAULT 0)$tail",
        jjt_sql_statistik($driver),
    ];
}

/* Statistik pelawat: satu baris setiap view page / klik butang (tiada data peribadi) */
function jjt_sql_statistik(string $driver): string
{
    $my = $driver === 'mysql';
    $ai = $my ? 'INT AUTO_INCREMENT PRIMARY KEY' : 'INTEGER PRIMARY KEY AUTOINCREMENT';
    $tail = $my ? ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci' : '';
    return "CREATE TABLE IF NOT EXISTS statistik (id $ai, masa VARCHAR(20), tarikh VARCHAR(10), jenis VARCHAR(10), page VARCHAR(190), sasaran VARCHAR(40),
        pelawat VARCHAR(16), peranti VARCHAR(10), sumber VARCHAR(40)" . ($my ? ', INDEX ix_stat_tarikh (tarikh)' : '') . ")$tail";
}

/* Contoh Butiran Harga, Tempat Menarik & cuaca untuk pakej Jepun (staf boleh ubah di dashboard) */
function jjt_contoh_data(): array
{
    $u = fn($id) => "https://images.unsplash.com/photo-$id?w=800&h=600&fit=crop&q=75&fm=jpg";
    return [
        'harga_detail' => ['kol' => ['Min. 4', 'Min. 6', 'Min. 8'],
            'baris' => [['n' => 'Dewasa', 's' => '12 tahun ke atas', 'v' => ['6688', '6288', '6088']],
                ['n' => 'Kanak-kanak dengan katil', 's' => '5 – 11 tahun', 'v' => ['6488', '6088', '5888']],
                ['n' => 'Kanak-kanak tanpa katil', 's' => '2 – 4 tahun', 'v' => ['6288', '5888', '5688']]],
            'lain' => [['n' => 'Single supplement', 's' => 'Bilik seorang', 'v' => 'Tambahan RM1,800 seorang / trip'],
                ['n' => 'Bayi', 's' => 'Bawah 2 tahun', 'v' => 'RM1,000']],
            'nota' => 'Harga seorang. "Min." ialah bilangan minimum peserta dalam satu kumpulan.'],
        'tarikan' => [['n' => 'Kuil Senso-ji, Asakusa', 'img' => $u('1542090518-6c4131442225')], ['n' => 'Persimpangan Shibuya', 'img' => $u('1542051841857-5f90071e7989')],
            ['n' => 'Pagoda Chureito & Gunung Fuji', 'img' => $u('1528164344705-47542687000d')], ['n' => 'Kuil Fushimi Inari', 'img' => $u('1613487691352-7d9b4ee5045b')],
            ['n' => 'Hutan Buluh Arashiyama', 'img' => $u('1632923754832-60642c12a7ed')], ['n' => 'Jambatan Togetsukyo', 'img' => $u('1786725130631-6fe9691e085a')],
            ['n' => 'Taman Rusa Nara', 'img' => $u('1693380767492-00fb0bcc4ce6')], ['n' => 'Istana Osaka', 'img' => $u('1596240748549-6ec0f32d4c95')],
            ['n' => 'Shinsaibashi & Dotonbori', 'img' => $u('1546142711-1e28c0540deb')], ['n' => 'Gunung Fuji', 'img' => $u('1526481280693-3bfa7568e0f3')]],
        'cuaca' => [['m' => 'Musim sejuk', 't' => '-1°C – 4°C', 'b' => 'Disember – Februari'], ['m' => 'Musim bunga', 't' => '8°C – 15°C', 'b' => 'Mac – Mei'],
            ['m' => 'Musim panas', 't' => '18°C – 28°C', 'b' => 'Jun – Ogos / September'], ['m' => 'Musim luruh', 't' => '8°C – 15°C', 'b' => 'September / Oktober – November']],
    ];
}
function jjt_contoh_semua(PDO $db): void
{
    $f = __DIR__ . '/contoh.json';
    if (!is_file($f)) return;
    $C = json_decode((string)file_get_contents($f), true) ?: [];
    $j = fn($v) => json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $u = fn($id) => "https://images.unsplash.com/photo-$id?w=800&h=600&fit=crop&q=75&fm=jpg";
    $kosong = fn($v) => $v === null || $v === '' || $v === '[]' || $v === 'null';
    $up = $db->prepare('UPDATE pakej SET tarikan = ?, cuaca = ? WHERE id = ?');
    foreach ($db->query('SELECT id, dest, tarikan, cuaca FROM pakej')->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $t = $r['tarikan']; $c = $r['cuaca'];
        if ($kosong($t) && !empty($C['pakej'][$r['id']])) $t = $j(array_map(fn($x) => ['n' => $x[0], 'img' => $u($x[1])], $C['pakej'][$r['id']]));
        $kunci = $r['id'] === 'jepun-adventure-hokkaido' ? 'jepun-hokkaido' : $r['dest'];
        if ($kosong($c) && !empty($C['cuaca'][$kunci])) $c = $j($C['cuaca'][$kunci]);
        if ($t !== $r['tarikan'] || $c !== $r['cuaca']) $up->execute([$t, $c, $r['id']]);
    }
}
function jjt_contoh_pakej(PDO $db): void
{
    $c = jjt_contoh_data();
    $j = fn($v) => json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $db->prepare('UPDATE pakej SET harga_detail = ?, tarikan = ?, cuaca = ? WHERE id = ? AND (harga_detail IS NULL OR harga_detail = \'\' OR harga_detail = \'null\')')
        ->execute([$j($c['harga_detail']), $j($c['tarikan']), $j($c['cuaca']), 'jepun-private-tokyo-osaka']);
}

function jjt_seed(PDO $db, array $S): array
{
    $j = fn($v) => json_encode($v, JSON_UNESCAPED_UNICODE);
    $ins = function (string $t, array $row) use ($db) {
        $c = array_keys($row);
        $st = $db->prepare("INSERT INTO $t (" . implode(',', $c) . ') VALUES (' . implode(',', array_fill(0, count($c), '?')) . ')');
        $st->execute(array_values($row));
    };
    $n = [];
    foreach ($S['tetapan'] as $k => $v) $ins('tetapan', ['k' => $k, 'v' => $v]);
    foreach ($S['jenis'] as $i => $x) $ins('jenis', ['id' => $x['id'], 'nama' => $x['nama'], 'ikon' => $x['ikon'], 'ringkas' => $x['ringkas'], 'susunan' => $i]);
    foreach ($S['kawasan'] as $i => $x) $ins('kawasan', ['id' => $x['id'], 'nama' => $x['nama'], 'susunan' => $i]);
    foreach ($S['destinasi'] as $i => $x) $ins('destinasi', ['id' => $x['id'], 'nama' => $x['nama'], 'pendek' => $x['pendek'] ?? null, 'kawasan' => $x['kawasan'],
        'img' => $x['img'], 'musim' => $x['musim'], 'matawang' => $x['matawang'], 'terbang' => $x['terbang'], 'visa' => $x['visa'], 'intro' => $x['intro'], 'susunan' => $i]);
    foreach ($S['pakej'] as $i => $x) $ins('pakej', ['id' => $x['id'], 'dest' => $x['dest'], 'jenis' => $x['jenis'], 'nama' => $x['nama'], 'hari' => $x['hari'], 'malam' => $x['malam'],
        'harga' => $x['harga'], 'hotel' => $x['hotel'], 'kumpulan' => $x['kumpulan'], 'badge' => $x['badge'] ?? null, 'laluan' => $j($x['laluan']), 'tarikh' => $j($x['tarikh']),
        'sorotan' => $j($x['sorotan']), 'itinerari' => $j($x['itinerari']), 'termasuk' => $j($x['termasuk']), 'tidak' => $j($x['tidak']), 'nota' => $j($x['nota']),
        'susunan' => $i, 'dikemaskini' => date('Y-m-d H:i:s')]);
    foreach ($S['testimoni'] as $i => $x) $ins('testimoni', ['nama' => $x['n'], 'pakej' => $x['p'], 'img' => $x['img'], 'teks' => $x['q'], 'susunan' => $i]);
    foreach ($S['blog'] as $x) $ins('blog', ['id' => $x['id'], 'tajuk' => $x['tajuk'], 'kat' => $x['kat'], 'img' => $x['img'], 'tarikh' => $x['tarikh'], 'minit' => $x['minit'],
        'dest' => $x['dest'], 'ringkas' => $x['ringkas'], 'meta' => $x['meta'], 'isi' => $x['isi']]);
    foreach (['destinasi', 'pakej', 'testimoni', 'blog'] as $t) $n[$t] = count($S[$t]);
    return $n;
}

/* Kemas kini database sedia ada (tambah kolum/jadual baharu). Selamat dijalankan berulang kali. */
function jjt_migrasi(PDO $db, string $driver): void
{
    foreach (jjt_schema($driver) as $sql) $db->exec($sql);   // jadual baharu sahaja (IF NOT EXISTS)
    if ($driver === 'sqlite') $db->exec('CREATE INDEX IF NOT EXISTS ix_stat_tarikh ON statistik (tarikh)');
    $tambah = [
        ['destinasi', 'papar_utama', 'TINYINT DEFAULT 1'], ['pakej', 'img', 'VARCHAR(255)'], ['pakej', 'harga_asal', 'INT'], ['testimoni', 'sumber', 'VARCHAR(60)'], ['testimoni', 'gambar', 'TEXT'],
        ['pengguna', 'akses', 'TEXT'], ['pengguna', 'wajib_tukar', 'TINYINT DEFAULT 0'], ['inquiry', 'pax', 'TEXT'], ['inquiry', 'anggaran', 'INT'], ['inquiry', 'setuju', 'TINYINT'], ['inquiry', 'perkara', 'VARCHAR(160)'], ['inquiry', 'catatan', 'TEXT'], ['inquiry', 'pilihan', 'VARCHAR(80)'], ['pakej', 'harga_detail', 'TEXT'], ['pakej', 'tarikan', 'TEXT'], ['pakej', 'cuaca', 'TEXT'],
    ];
    $baru = [];
    foreach ($tambah as [$t, $c, $def]) {
        try { $db->query("SELECT $c FROM $t LIMIT 1"); } catch (Throwable $e) { $db->exec("ALTER TABLE $t ADD COLUMN $c $def"); $baru[] = "$t.$c"; }
    }
    // contoh Butiran Harga / Tempat Menarik untuk pakej Jepun — sekali sahaja (ditanda dalam tetapan)
    try {
        if (!(bool)$db->query("SELECT COUNT(*) FROM tetapan WHERE k = 'contoh_pakej'")->fetchColumn()) {
            jjt_contoh_pakej($db);
            $db->exec("INSERT INTO tetapan (k, v) VALUES ('contoh_pakej', '1')");
        }
    } catch (Throwable $e) {}
    try {   // Tempat Menarik & cuaca untuk pakej lain — sekali sahaja, tidak menimpa data yang staf sudah isi
        if (!(bool)$db->query("SELECT COUNT(*) FROM tetapan WHERE k = 'contoh_pakej2'")->fetchColumn()) {
            jjt_contoh_semua($db);
            $db->exec("INSERT INTO tetapan (k, v) VALUES ('contoh_pakej2', '1')");
        }
    } catch (Throwable $e) {}
    try {   // pakej baharu diminta client: Sapporo, Hokkaido (promo) — sekali sahaja, tidak ditambah semula jika staf memadamnya
        if (!(bool)$db->query("SELECT COUNT(*) FROM tetapan WHERE k = 'pakej_sapporo'")->fetchColumn()) {
            if (!(bool)$db->query("SELECT COUNT(*) FROM pakej WHERE id = 'jepun-private-sapporo-hokkaido'")->fetchColumn()) {
                $sus = (int)$db->query('SELECT COALESCE(MAX(susunan), 0) + 1 FROM pakej')->fetchColumn();
                $j = fn($v) => json_encode($v, JSON_UNESCAPED_UNICODE);
                $db->prepare('INSERT INTO pakej (id, dest, jenis, nama, hari, malam, harga, harga_asal, hotel, kumpulan, badge, laluan, tarikh, sorotan, itinerari, termasuk, tidak, nota, susunan, aktif, dikemaskini)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')->execute(['jepun-private-sapporo-hokkaido', 'jepun', 'private', 'Sapporo, Hokkaido', 0, 0, 8888, 9588, '', 'Private', 'Promo',
                    $j([]), $j('fleksibel'), $j([]), $j([]), $j(['Pengangkutan persendirian (private transport) sepanjang trip']), $j([]),
                    $j(['Harga promosi RM8,888 seorang untuk pilihan Standard (private transport sepanjang trip). Harga asal RM9,588 seorang.']), $sus, 1, date('Y-m-d H:i:s')]);
            }
            $db->exec("INSERT INTO tetapan (k, v) VALUES ('pakej_sapporo', '1')");
        }
    } catch (Throwable $e) { error_log('JJT pakej Sapporo: ' . $e->getMessage()); }
    $ada = fn($k) => (bool)$db->query("SELECT COUNT(*) FROM tetapan WHERE k = " . $db->quote($k))->fetchColumn();
    // alamat asal satu baris → baris berasingan (sama seperti footer asal)
    $db->prepare('UPDATE tetapan SET v = ? WHERE k = ? AND v = ?')->execute(["No. 5, Pusat Perdagangan Hillpark 11/1A,\nPersiaran Hillpark, Bandar Hillpark,\n42300 Puncak Alam, Selangor.", 'alamat', 'No. 5, Pusat Perdagangan Hillpark 11/1A, Persiaran Hillpark, Bandar Hillpark, 42300 Puncak Alam, Selangor']);
    foreach (['waktu' => 'Isnin – Jumaat, 10.00 pagi – 5.30 petang (Selain Cuti Umum)', 'notif' => 'info@jalanjalantravel.com', 'pratonton' => bin2hex(random_bytes(8))] as $k => $v)
        if (!$ada($k)) $db->prepare('INSERT INTO tetapan (k, v) VALUES (?, ?)')->execute([$k, $v]);
}
