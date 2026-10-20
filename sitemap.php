<?php
require __DIR__ . '/inc/db.php';
require __DIR__ . '/inc/fn.php';
require __DIR__ . '/inc/data.php';
require __DIR__ . '/inc/kandungan.php';
header('Content-Type: application/xml; charset=utf-8');
$U = [['', '1.0'], ['pakej', '0.9'], ['blog', '0.8'], ['tentang', '0.6'], ['testimoni', '0.6'], ['hubungi', '0.6'], ['kerjaya', '0.4'], ['foundation', '0.4'],
      ['polisi/privasi', '0.2'], ['polisi/penafian', '0.2'], ['polisi/bayaran', '0.2'], ['polisi/terma', '0.2'], ['inquiry', '0.5'], ['itinerari', '0.7']];
$hid = ['pakej' => 'pakej', 'blog' => 'blog', 'tentang' => 'tentang', 'testimoni' => 'testimoni', 'hubungi' => 'hubungi', 'kerjaya' => 'kerjaya', 'foundation' => 'foundation', 'polisi' => 'polisi', 'inquiry' => 'inquiry'];
$U = array_values(array_filter($U, function ($x) use ($hid) { $k = explode('/', explode('?', $x[0])[0])[0]; return $k === '' || !isset($hid[$k]) || hidup($k); }));
if (hidup('pakej')) foreach (semua_jenis() as $j) $U[] = ['pakej?jenis=' . $j['id'], '0.8'];
if (hidup('pakej')) foreach (semua_destinasi() as $d) if (pakej_dest($d['id'])) $U[] = ['destinasi/' . $d['id'], '0.8'];
if (hidup('pakej')) foreach (semua_pakej() as $p) $U[] = ['pakej/' . $p['id'], '0.7', substr((string)$p['dikemaskini'], 0, 10)];
if (hidup('blog')) foreach (semua_blog() as $b) $U[] = ['blog/' . $b['id'], '0.7', $b['tarikh']];
echo '<?xml version="1.0" encoding="UTF-8"?>', "\n", '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', "\n";
foreach ($U as $x) {
    echo '  <url><loc>', htmlspecialchars(url_penuh($x[0]), ENT_XML1), '</loc>', !empty($x[2]) ? '<lastmod>' . $x[2] . '</lastmod>' : '', '<priority>', $x[1], '</priority></url>', "\n";
}
echo '</urlset>', "\n";
