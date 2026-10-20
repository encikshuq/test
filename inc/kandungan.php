<?php
/* Kandungan yang boleh diubah di dashboard (/jjt-panel).
   Nilai di bawah ialah lalai = teks asal website. Dashboard menyimpan versi baharu dalam jadual `kandungan`. */

function kandungan_lalai(): array
{
    $pol = function (array $s) {
        $o = '';
        foreach ($s as $i => [$h, $t]) $o .= '<h2>' . ($i + 1) . '. ' . htmlspecialchars($h) . '</h2><p>' . htmlspecialchars($t) . '</p>';
        return $o;
    };
    return [
        'utama' => [
            'seo' => 'Jom Jalan-Jalan Pusing Dunia — Pakej Percutian Muslim Paling Best', 'hero' => 'images/hero.jpg',
            'kenapa' => [['t' => 'Pakar Private Trip', 'a' => 'Percutian lebih personal & selesa'], ['t' => 'Itinerari Santai & Tersusun', 'a' => 'Trip smooth, tak rushing'],
                ['t' => 'No Tourist Trap', 'a' => 'Lebih masa untuk tempat menarik'], ['t' => 'Muslim-Friendly Trip', 'a' => 'Makan halal & solat diutamakan']],
            'jenisKecil' => 'Pilih Jenis Trip', 'jenisTajuk' => 'Pakej Mengikut Gaya Percutian Anda',
            'destKecil' => 'Pilih Destinasi', 'destTajuk' => 'Pakej Destinasi Private Trip Anda', 'destAyat' => 'Jelajah lebih jauh, pengalaman luar biasa bersama kumpulan sehata tuju.',
            'seriesAyat' => 'Series Trip ialah perjalanan bersiri dengan tarikh yang telah ditetapkan setiap bulan. Terdapat dua destinasi pilihan untuk setiap bulan.',
            'testiKecil' => 'Testimoni Pelanggan', 'testiTajuk' => 'Apa Kata Mereka Tentang Kami',
            'ctaTajuk' => 'Masih tercari-cari destinasi terbaik?', 'ctaAyat' => 'Tanya kami, dapatkan cadangan percuma mengikut tarikh, bajet dan kumpulan anda.', 'ctaButang' => 'Tanya Sekarang',
            'blogPapar' => true, 'blogKecil' => 'Blog & Tips Travel', 'blogTajuk' => 'Panduan Sebelum Berlepas',
        ],
        'tentangX' => ['misi' => 'Menyediakan pengalaman melancong yang selamat, patuh syariah dan berbaloi untuk setiap keluarga Muslim.',
            'visi' => 'Menjadi agensi pelancongan Muslim pilihan utama di Malaysia untuk destinasi seluruh dunia.',
            'nilai' => 'Amanah, telus dalam harga, mengutamakan keselesaan dan sentiasa mendengar maklum balas pelanggan.', 'kpk' => 'L/N/11783', 'matta' => '', 'ssm' => '1545479-H',
            // Reka bentuk baharu halaman Tentang Kami
            'hero_tajuk' => 'Jalan Jalan Travel', 'slogan' => 'Beyond Ordinary Travel',
            'hero_ayat' => "Sejak 2016, kami membantu rakyat Malaysia meneroka dunia dengan cara yang lebih mudah, selesa dan penuh pengalaman bermakna.\nBagi kami, setiap perjalanan ada cerita dan pengalaman tersendiri.",
            'hero_img' => 'images/jepun.jpg',
            'stat' => [['v' => '2016', 'l' => 'Bermula Sejak'], ['v' => '40+', 'l' => 'Destinasi Dunia'], ['v' => '5,000+', 'l' => 'Pelanggan'], ['v' => '4.9 ★', 'l' => 'Rating Pelanggan']],
            'kisah_tajuk' => 'Bermula Sejak 2016, Terus Melangkah Lebih Jauh',
            'kisah_ayat' => "Perjalanan kami dalam industri travel bermula sejak tahun 2016. Dari situ, kami terus berkembang dan membantu lebih ramai rakyat Malaysia menikmati percutian ke luar negara.\nHari ini, **Jalan Jalan Travel & Tours International Sdn Bhd** merupakan sebuah syarikat pelancongan milik Bumiputera Muslim yang memberi fokus kepada percutian mesra Muslim dan Private Trip.",
            'petikan_akhir' => 'Itulah yang kami ingin bawakan melalui',
            'petikan' => 'Bagi kami, travel bukan sekadar pergi ke tempat menarik. Kami nak setiap perjalanan jadi lebih daripada percutian biasa, dengan pengalaman yang berbeza, keselesaan yang terjaga dan kenangan bermakna bersama orang tersayang.',
            'foto1' => 'images/asia-tengah.jpg', 'foto2' => 'images/jordan.jpg', 'foto_nota' => 'Lebih daripada percutian biasa',
            'kenapa' => [['t' => 'Private Trip, Lebih Personal', 'a' => 'Travel bersama keluarga atau kawan-kawan sendiri. Lebih privasi, lebih fleksibel dan tak perlu bercampur dengan kumpulan lain.'],
                ['t' => 'Muslim-Friendly Travel', 'a' => 'Kami ambil kira keperluan pelancong Muslim, termasuk makanan halal, waktu solat dan keselesaan sepanjang perjalanan.'],
                ['t' => 'Tour Leader Melayu', 'a' => 'Lebih mudah berkomunikasi dan mendapatkan bantuan sepanjang trip, bagi pakej yang disertakan Tour Leader.'],
                ['t' => 'Perjalanan Lebih Terancang', 'a' => 'Daripada hotel, transport hingga aktiviti, kami bantu uruskan supaya anda boleh fokus menikmati pengalaman sepanjang percutian.']],
            'pasukan_tajuk' => 'Orang Di Sebalik Jalan Jalan Travel',
            'pasukan_ayat' => "Di sebalik setiap trip, ada team yang membantu merancang dan menguruskan perjalanan pelanggan.\nDaripada mula bertanya tentang pakej sampailah hari perjalanan, kami berusaha memastikan setiap urusan berjalan dengan baik.\nBagi kami, pengalaman travel yang baik bermula dengan layanan yang baik dan kepercayaan pelanggan yang sentiasa dijaga.",
            'pasukan_img' => 'images/team-jjt.jpg', 'lesen_url' => '',
            'lesen_ayat' => "**Jalan Jalan Travel & Tours International Sdn Bhd** merupakan syarikat pelancongan yang berdaftar dengan Suruhanjaya Syarikat Malaysia (SSM) dan berlesen dengan Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC).\nKami komited untuk menguruskan setiap perjalanan secara profesional dan bertanggungjawab.",
            'alamat' => "No. 5, Pusat Perdagangan Hillpark 11A,\nPersiaran Hillpark,\nPuncak Alam, Selangor.",
            'kenangan_ayat' => "Bagi kami, antara perkara yang paling menggembirakan ialah melihat pelanggan **enjoy** sepanjang trip bersama keluarga dan kawan-kawan.\nAda yang pertama kali melihat salji, ada yang akhirnya sampai ke destinasi impian, dan ada juga yang dapat meluangkan masa berkualiti bersama orang tersayang.\nInilah pengalaman dan kenangan yang menjadikan **setiap perjalanan lebih istimewa**.",
            'galeri' => ['images/jepun.jpg', 'images/new-zealand.jpg', 'images/asia-tengah.jpg', 'images/alaska.jpg', 'images/mesir.jpg'],
            'cta_tajuk' => 'Jom Cipta Pengalaman Travel Yang Berbeza!',
            'cta_ayat' => "Dah ada destinasi yang teringin nak pergi?\nSama ada nak travel bersama keluarga, kawan-kawan atau rakan sekerja, kongsikan perancangan anda dengan team kami.\nKami bantu cadangkan pakej yang sesuai dengan bajet, keperluan dan pengalaman yang anda cari.",
            'cta_img' => 'images/jepun.jpg'],
        'program' => [['id' => 'p1', 't' => 'Bantuan Asnaf', 'a' => 'Sumbangan keperluan asas kepada keluarga yang memerlukan.'],
            ['id' => 'p2', 't' => 'Pendidikan Anak Yatim', 'a' => 'Tajaan peralatan sekolah dan program motivasi.'],
            ['id' => 'p3', 't' => 'Komuniti Masjid', 'a' => 'Program bersama masjid dan surau di kawasan setempat.']],
        'jawatan' => [['id' => 'j1', 't' => 'Eksekutif Jualan Pelancongan', 'j' => 'Sepenuh masa', 'l' => 'Puncak Alam, Selangor', 'd' => 'Menguruskan inquiry pelanggan, menyediakan sebut harga dan membantu tempahan pakej.', 'buka' => true],
            ['id' => 'j2', 't' => 'Tour Leader', 'j' => 'Kontrak / sambilan', 'l' => 'Mengikut trip', 'd' => 'Mengetuai kumpulan pelancong ke luar negara, memastikan jadual, solat dan makan halal terurus.', 'buka' => true],
            ['id' => 'j3', 't' => 'Pereka Grafik & Media Sosial', 'j' => 'Internship (3–6 bulan)', 'l' => 'Puncak Alam, Selangor', 'd' => 'Menghasilkan poster promosi, video pendek dan kandungan media sosial JJT.', 'buka' => true]],
        'foundation' => ['penerima' => '', 'program' => '', 'sukarelawan' => '', 'negeri' => ''],
        'borang' => [
            'bilik' => [['id' => 'twin', 'n' => 'Twin / Double', 'k' => '2 orang sebilik'], ['id' => 'triple', 'n' => 'Triple', 'k' => '3 orang sebilik'], ['id' => 'single', 'n' => 'Single', 'k' => '1 orang sebilik']],
            'negeri' => ['Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Perak', 'Perlis', 'Pulau Pinang', 'Sabah', 'Sarawak', 'Selangor', 'Terengganu', 'W.P. Kuala Lumpur', 'W.P. Putrajaya', 'W.P. Labuan', 'Luar negara'],
            'perkara' => ['Pertanyaan pakej', 'Tempahan sedia ada', 'Visa & dokumen', 'Kerjasama / korporat', 'Lain-lain'],
        ],
        'footer' => array_map(fn($r) => ['id' => 'f' . $r[2], 'l' => $r[0], 't' => $r[1], 'v' => $r[2]], [['Pakej Jepun', 'd', 'jepun'], ['Pakej Korea', 'd', 'korea'], ['Pakej China', 'd', 'china'],
            ['Pakej Eropah', 'd', 'eropah'], ['Pakej Australia', 'd', 'australia'], ['Pakej Asean', 'd', 'asean'], ['Pakej Sempurna (Series)', 'j', 'series'], ['Pulau Perhentian', 'd', 'perhentian']]),
        'halaman' => [
            'tentang' => ['nama' => 'Tentang Kami', 'html' => '<p>Jalan Jalan Travel &amp; Tour International Sdn Bhd ditubuhkan untuk memudahkan keluarga Muslim Malaysia mengembara ke seluruh dunia tanpa risau tentang makanan, solat dan keselamatan.</p><p>Setiap pakej dirancang oleh pasukan yang pernah sendiri menjejak destinasi tersebut — supaya perjalanan anda santai, tersusun dan penuh makna.</p>'],
            'foundation' => ['nama' => 'JJT Foundation', 'html' => '<p>JJT Foundation ialah inisiatif kebajikan Jalan Jalan Travel untuk membantu golongan asnaf, anak yatim dan komuniti setempat.</p><p>Penerangan penuh, laporan aktiviti dan gambar program akan dikemaskini oleh JJT.</p>'],
            'kerjaya' => ['nama' => 'Kerjaya / Internship', 'html' => '<p>Minat dunia pelancongan? Sertai pasukan JJT dan bantu keluarga Muslim menjelajah dunia.</p>'],
            'hubungi' => ['nama' => 'Hubungi Kami', 'html' => '<p>Ada soalan tentang pakej, visa atau tarikh? Pasukan kami sedia membantu.</p>'],
            'privasi' => ['nama' => 'Polisi: Privasi', 'html' => $pol([['Maklumat yang kami kumpul', 'Nama, nombor telefon, emel, maklumat pasport dan butiran perjalanan yang anda berikan semasa membuat inquiry atau tempahan.'], ['Tujuan penggunaan', 'Untuk memproses tempahan, menguruskan visa, tiket dan penginapan, serta menghubungi anda berkenaan perjalanan.'], ['Perkongsian maklumat', 'Maklumat hanya dikongsi dengan pihak ketiga yang berkaitan (syarikat penerbangan, hotel, kedutaan) untuk tujuan perjalanan anda.'], ['Hak anda', 'Anda boleh meminta salinan, pembetulan atau pemadaman data peribadi dengan menghubungi kami.']])],
            'penafian' => ['nama' => 'Polisi: Penafian', 'html' => $pol([['Ketepatan maklumat', 'Kami berusaha memastikan semua maklumat di laman ini tepat, namun harga, jadual dan itinerari boleh berubah tanpa notis.'], ['Pihak ketiga', 'JJT tidak bertanggungjawab atas kelewatan, pembatalan atau perubahan oleh syarikat penerbangan, hotel atau pihak berkuasa.'], ['Pautan luar', 'Laman ini mungkin mengandungi pautan ke laman luar yang tidak dikawal oleh JJT.']])],
            'bayaran' => ['nama' => 'Polisi: Bayaran', 'html' => $pol([['Deposit', 'Deposit diperlukan untuk mengesahkan tempahan. Jumlah deposit bergantung kepada pakej.'], ['Baki bayaran', 'Baki perlu dijelaskan sebelum tarikh akhir yang dinyatakan dalam invois.'], ['Kaedah bayaran', 'Pemindahan bank, FPX atau kaedah lain yang dinyatakan oleh JJT.'], ['Pembatalan & bayaran balik', 'Caj pembatalan dikenakan mengikut tempoh notis sebelum tarikh berlepas.']])],
            'terma' => ['nama' => 'Polisi: Terma & Syarat', 'html' => $pol([['Tempahan', 'Tempahan hanya sah selepas deposit diterima dan pengesahan bertulis dikeluarkan oleh JJT.'], ['Dokumen perjalanan', 'Peserta bertanggungjawab memastikan pasport sah sekurang-kurangnya 6 bulan dan memenuhi syarat visa.'], ['Perubahan itinerari', 'JJT berhak mengubah itinerari atas faktor cuaca, keselamatan atau keadaan di luar kawalan.'], ['Insurans', 'Peserta digalakkan mempunyai insurans perjalanan yang mencukupi.']])],
        ],
        'hidup' => ['pakej' => true, 'inquiry' => true, 'blog' => true, 'tentang' => true, 'testimoni' => true, 'hubungi' => true, 'kerjaya' => true, 'foundation' => true, 'polisi' => true],
        'banner' => [],
        /* Video YouTube Shorts di halaman Testimoni (contoh sementara — tukar di dashboard: Testimoni → Video Shorts) */
        'shorts' => [
            ['u' => 'https://www.youtube.com/shorts/Tbj5dTOE_ig', 't' => 'Tips Muslim ke Jepun'],
            ['u' => 'https://www.youtube.com/shorts/0Z5FwTAx2DI', 't' => 'Makanan halal di Jepun'],
            ['u' => 'https://www.youtube.com/shorts/jW2A5B-QxrY', 't' => 'Muslim di Tokyo'],
            ['u' => 'https://www.youtube.com/shorts/WjZzE3jpU_g', 't' => 'Restoran mesra Muslim di Seoul'],
            ['u' => 'https://www.youtube.com/shorts/P5anqPw94pc', 't' => 'Cari makanan halal di Seoul'],
        ],
    ];
}

const KANDUNGAN_KEYS = ['utama', 'tentangX', 'program', 'jawatan', 'foundation', 'borang', 'footer', 'halaman', 'hidup', 'banner', 'shorts'];

/* Baca satu kandungan: versi tersimpan digabung dengan lalai (medan baharu tetap ada) */
function kand(string $k)
{
    static $C = null;
    if ($C === null) {
        $C = [];
        try {
            foreach (q('SELECT k, v FROM kandungan')->fetchAll() as $r) $C[$r['k']] = json_decode((string)$r['v'], true);
        } catch (Throwable $e) { /* jadual belum wujud: guna lalai */ }
    }
    $L = kandungan_lalai()[$k] ?? null;
    $v = $C[$k] ?? null;
    if ($v === null) return $L;
    $assoc = is_array($L) && $L !== [] && array_keys($L) !== range(0, count($L) - 1);
    if ($assoc && is_array($v)) return array_replace($L, $v);
    return $v;
}

function hidup(string $k): bool
{
    $h = kand('hidup');
    return !isset($h[$k]) || (bool)$h[$k];
}

/* HTML dari editor dashboard: benarkan tag asas sahaja */
function html_selamat(string $html): string
{
    $html = strip_tags($html, '<p><br><b><strong><i><em><u><h2><h3><h4><ul><ol><li><a><div><span><table><thead><tbody><tr><th><td><svg><use><path><circle><rect><blockquote>');
    $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
    $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '$1="#"', $html);
    return $html;
}

/* Pratonton halaman yang OFF: link ?pratonton=kod, atau pentadbir yang sedang log masuk di dashboard */
function admin_log_masuk(): ?array
{
    static $u = false;
    if ($u !== false) return $u;
    $u = null;
    if (!empty($_COOKIE['jjtpanel'])) {
        if (session_status() === PHP_SESSION_NONE) { session_name('jjtpanel'); @session_start(['read_and_close' => true]); }
        $u = $_SESSION['pengguna'] ?? null;
    }
    return $u;
}
function boleh_pratonton(): bool
{
    $kod = tetapan('pratonton');
    if ($kod !== '' && isset($_GET['pratonton']) && hash_equals($kod, (string)$_GET['pratonton'])) {
        setcookie('jjt_pv', $kod, ['expires' => time() + 7 * 86400, 'path' => BASE, 'httponly' => true, 'samesite' => 'Lax']);
        return true;
    }
    if ($kod !== '' && isset($_COOKIE['jjt_pv']) && hash_equals($kod, (string)$_COOKIE['jjt_pv'])) return true;
    return (bool)admin_log_masuk();
}

/* ID video YouTube daripada pautan Shorts / watch / youtu.be / embed (atau ID sahaja) */
function yt_id(string $u): string
{
    $u = trim($u);
    if (preg_match('~^[A-Za-z0-9_-]{11}$~', $u)) return $u;
    if (preg_match('~(?:youtube(?:-nocookie)?\.com/(?:shorts/|embed/|live/|watch\?(?:.*&)?v=)|youtu\.be/)([A-Za-z0-9_-]{11})~', $u, $m)) return $m[1];
    return '';
}

/* Kenal pasti video YouTube atau TikTok daripada pautan.
   Pulangkan ['k' => 'yt'|'tt', 'id' => …, 'u' => pautan kemas] atau null. Pautan pendek TikTok (vt./vm.tiktok.com) perlu dibuka dahulu — lihat tiktok_buka(). */
function video_info(string $u): ?array
{
    $u = trim($u);
    if ($id = yt_id($u)) return ['k' => 'yt', 'id' => $id, 'u' => 'https://www.youtube.com/shorts/' . $id];
    if (preg_match('~tiktok\.com/(@[\w.-]+)/(?:video|photo)/(\d{8,24})~i', $u, $m)) return ['k' => 'tt', 'id' => $m[2], 'u' => 'https://www.tiktok.com/' . $m[1] . '/video/' . $m[2]];
    if (preg_match('~tiktok\.com/(?:embed(?:/v2)?|player/v1|video|v)/(\d{8,24})~i', $u, $m)) return ['k' => 'tt', 'id' => $m[1], 'u' => 'https://www.tiktok.com/player/v1/' . $m[1]];
    return null;
}

/* Buka pautan pendek TikTok (vt.tiktok.com/…, vm.tiktok.com/…, tiktok.com/t/…) untuk dapatkan pautan penuh */
function tiktok_buka(string $u): ?string
{
    if (!preg_match('~^https?://(?:(?:vt|vm)\.tiktok\.com/[\w-]+|(?:www\.)?tiktok\.com/t/[\w-]+)/?~i', trim($u), $m)) return null;
    if (!function_exists('curl_init')) return null;
    $c = curl_init($m[0]);
    curl_setopt_array($c, [CURLOPT_FOLLOWLOCATION => true, CURLOPT_MAXREDIRS => 5, CURLOPT_RETURNTRANSFER => true, CURLOPT_NOBODY => true, CURLOPT_TIMEOUT => 8,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36']);
    curl_exec($c);
    $akhir = (string)curl_getinfo($c, CURLINFO_EFFECTIVE_URL);
    curl_close($c);
    return ($v = video_info($akhir)) && $v['k'] === 'tt' ? $v['u'] : null;
}

