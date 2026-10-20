<?php
require __DIR__ . '/inc/layout.php';

/* Destinasi ikut jenis trip (paparan awal: Private Trip) */
function senarai_dest_jenis(string $jenisId): array
{
    $ps = array_filter(semua_pakej(), fn($p) => $p['jenis'] === $jenisId);
    $out = [];
    foreach (semua_destinasi() as $d) {
        if (isset($d['papar_utama']) && !(int)$d['papar_utama']) continue;   // "Tunjuk di halaman Utama" (dashboard)
        $x = array_filter($ps, fn($p) => $p['dest'] === $d['id']);
        if ($x) $out[] = ['d' => $d, 'n' => count($x), 'min' => min(array_column($x, 'harga'))];
    }
    usort($out, fn($a, $b) => (int)!!$b['d']['img'] - (int)!!$a['d']['img']);
    return array_slice($out, 0, 9);
}
$J = semua_jenis();
$awal = jenis('private') ?: $J[0];
$list = senarai_dest_jenis($awal['id']);
$U = kand('utama');
$testi = array_slice(array_values(array_filter(semua_testimoni(), fn($t) => trim((string)$t['teks']) !== '')), 0, 4);   // review gambar sahaja (tanpa teks) tidak dipapar di Utama
$pakejOn = hidup('pakej');
$pv = isset($_GET['pv']) && admin_log_masuk();          // pratonton dalam dashboard
// Seksyen Blog boleh disembunyikan sementara di dashboard (cth. semasa kempen blast pakej)
$blogPapar = !empty($U['blogPapar']) && hidup('blog');
$blog = ($blogPapar || $pv) ? array_slice(semua_blog(), 0, 3) : [];
$waJenis = fn($j) => wa_link('Assalamualaikum JJT, saya berminat dengan pakej ' . $j['nama'] . '. Boleh kongsikan pilihan yang ada?');
/* Tajuk dengan bahagian berwarna: $n perkataan pertama biasa, selebihnya <em> */
function tajuk_em(string $t, int $n, string $emAttr = ''): string
{
    $w = preg_split('/\s+/', trim($t)); $n = min($n, max(0, count($w) - 1));
    return h(implode(' ', array_slice($w, 0, $n))) . ($n ? ' ' : '') . '<em' . $emAttr . '>' . h(implode(' ', array_slice($w, $n))) . '</em>';
}
?><!DOCTYPE html>
<html lang="ms">
<head>
<?php seo_head([
    'title' => $U['seo'] ?: 'Jalan Jalan Travel — Pakej Percutian Muslim Ke Seluruh Dunia',
    'desc' => 'Jom jalan-jalan pusing dunia bersama Jalan Jalan Travel. Pakej Private, Series, Honeymoon & Adventure Trip yang Muslim-friendly — makan halal, solat diutamakan, itinerari santai.',
    'path' => '',
    'jsonld' => [
        '@context' => 'https://schema.org', '@type' => 'TravelAgency', 'name' => 'Jalan Jalan Travel & Tour International Sdn Bhd',
        'alternateName' => 'JJT', 'url' => url_penuh(), 'logo' => url_penuh('images/logo.png'), 'image' => url_penuh($U['hero'] ?: 'images/hero.jpg'),
        'telephone' => '+' . tetapan('wa'), 'email' => tetapan('emel'),
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => 'No. 5, Pusat Perdagangan Hillpark 11/1A, Persiaran Hillpark, Bandar Hillpark', 'addressLocality' => 'Puncak Alam', 'addressRegion' => 'Selangor', 'postalCode' => '42300', 'addressCountry' => 'MY'],
        'sameAs' => array_values(array_filter([tetapan('fb'), tetapan('ig'), tetapan('tt')])),
    ],
]); ?>
<style>
<?php readfile(__DIR__ . '/inc/home.css'); ?>
</style>
<?= analitik_head() ?>
</head>
<body data-page="utama">

<?php readfile(__DIR__ . '/inc/home-sprite.svg'); ?>

<div class="page">

  <header class="hd">
    <a href="<?= u() ?>" class="logo"><img src="<?= u('images/logo.png') ?>" alt="JJT Jalan Jalan Travel"></a>
    <?= menu_atas('utama') ?>
    <div class="hd-r">
      <a class="hd-it" href="<?= u('itinerari') ?>"><svg class="ic"><use href="#f-route"/></svg>Free Quotation</a>
      <a class="hd-cta" href="<?= h(wa_link()) ?>" target="_blank" rel="noopener"><svg><use href="#wa"/></svg>Tanya Sekarang</a>
      <button class="hd-menu" aria-label="Menu" onclick="document.getElementById('dr').classList.add('open')"><svg class="ic"><use href="#menu"/></svg></button>
    </div>
  </header>

  <section class="hero">
    <h1 class="sr"><?= h($U['seo']) ?></h1>
    <div class="hero-bg" style="background-image:url(<?= h(img($U['hero'] ?: 'images/hero.jpg')) ?>)"></div>
    <img src="<?= h(img($U['hero'] ?: 'images/hero.jpg')) ?>" alt="<?= h($U['seo']) ?>">
  </section>

  <div class="feat ov" id="kenapa">
    <div class="feat-eb">Kenapa Pilih JJT</div>
<?php foreach ([['f-group', 'dot-heart'], ['f-route', 'dot-bulb'], ['f-shield', 'dot-check'], ['f-mosque', 'dot-moon']] as $i => [$ic1, $ic2]): $k = $U['kenapa'][$i] ?? null; if (!$k) continue; ?>
    <div class="f"><div class="f-ic"><svg class="ic"><use href="#<?= $ic1 ?>"/></svg><i><svg class="ic"><use href="#<?= $ic2 ?>"/></svg></i></div><b><?= h($k['t']) ?></b><span><?= h($k['a']) ?></span></div>
<?php endforeach; ?>
  </div>

  <section class="sec s-trip" id="jenis">
    <div class="eb"><?= h($U['jenisKecil']) ?></div>
    <h2 class="tt"><?= tajuk_em($U['jenisTajuk'], 2) ?></h2>
    <nav class="tbar" aria-label="Jenis trip">
<?php foreach ($J as $j): [$a, $b] = array_pad(explode(' ', $j['nama'], 2), 2, ''); ?>
    <a href="<?= h($pakejOn ? u('pakej?jenis=' . $j['id']) : $waJenis($j)) ?>"<?= $pakejOn ? '' : ' target="_blank" rel="noopener"' ?> class="tb<?= $j['id'] === $awal['id'] ? ' on' : '' ?>" data-j="<?= h($j['id']) ?>"><span class="tb-ic"><svg class="ic"><use href="#<?= h($j['ikon']) ?>"/></svg></span><b><?= h($a) ?><br><?= h($b) ?></b><svg class="ic tb-c"><use href="#chev"/></svg></a>
<?php endforeach; ?>
    </nav>
  </section>

  <section class="sec s-dest" id="destinasi">
<?php if ($pakejOn): ?>
    <div id="srs" hidden>
      <div class="srs-info"><svg class="ic"><use href="#cal"/></svg><p><?= preg_replace('/^(Series Trip)/u', '<b>$1</b>', h($U['seriesAyat'])) ?></p></div>
      <div class="eb">Jadual Series Departure</div>
      <h2 class="tt" id="srsT"></h2>
      <p class="st" id="srsSub"></p>
      <div class="srs-m" id="srsM" role="tablist" aria-label="Bulan"></div>
      <div class="srs-g" id="srsG"></div>
      <div class="more"><a href="<?= u('pakej?jenis=series') ?>" class="btn-o">Lihat Jadual Penuh <svg class="ic"><use href="#chev"/></svg></a></div>
    </div>
<?php endif; ?>
    <div id="destNorm">
    <div class="eb"><?= h($U['destKecil']) ?></div>
    <h2 class="tt"><?php $dw = preg_split('/\s+/', trim($U['destTajuk'])); echo h(implode(' ', array_slice($dw, 0, 2))); ?> <em id="tl"><?= h($awal['nama']) ?> Anda</em></h2>
    <p class="st" id="destSub"><?= h($U['destAyat'] ?: $awal['ringkas'] . ' ' . count($list) . ' destinasi pilihan untuk ' . $awal['nama'] . '.') ?></p>
    <div class="dest" id="destGrid">
<?php foreach ($list as $x): ?>
      <?= dest_card($x['d'], $pakejOn ? url_dest($x['d']['id'], $awal['id']) : wa_link('Assalamualaikum JJT, saya berminat dengan pakej ' . $awal['nama'] . ' ke ' . nama_pendek($x['d']) . '.'), $x['n'] . ' pakej · dari ' . rm($x['min'])) ?>

<?php endforeach; ?>
    </div>
<?php if ($pakejOn): ?>    <div class="more"><a href="<?= u('pakej?jenis=' . $awal['id']) ?>" class="btn-o" id="destAll">Lihat Semua <svg class="ic"><use href="#chev"/></svg></a></div><?php endif; ?>
    </div>
  </section>

<?php if ($blog): ?>
  <section class="sec s-blog" id="blog"<?= $blogPapar ? '' : ' style="display:none"' ?>>
    <div class="eb"><?= h($U['blogKecil']) ?></div>
    <h2 class="tt"><?= tajuk_em($U['blogTajuk'], 1) ?></h2>
    <p class="st">Tips praktikal untuk trip Muslim-friendly yang lebih tenang.</p>
    <div class="bl">
<?php foreach ($blog as $b): ?>
      <?= blog_card($b) ?>

<?php endforeach; ?>
    </div>
    <div class="more"><a href="<?= u('blog') ?>" class="btn-o">Semua Artikel <svg class="ic"><use href="#chev"/></svg></a></div>
  </section>
<?php endif; ?>

  <section class="sec s-testi" id="testimoni">
    <div class="eb"><?= h($U['testiKecil']) ?></div>
    <h2 class="tt dark"><?= tajuk_em($U['testiTajuk'], 3, ' class="mag"') ?></h2>
    <div class="tr" id="tr">
<?php foreach ($testi as $t): ?>
      <article class="tc"><div class="tc-h"><?php if ($t['img']): ?><img src="<?= h(img($t['img'])) ?>" alt=""><?php else: ?><span class="av-ph"><?= h(implode('', array_map(fn($w) => mb_substr($w, 0, 1), array_slice(explode(' ', $t['nama']), 0, 2)))) ?></span><?php endif; ?><div><div class="tc-n"><?= h($t['nama']) ?></div><div class="stars"><?= bintang() ?></div></div></div>
        <q><?= h($t['teks']) ?></q></article>
<?php endforeach; ?>
    </div>
    <div class="dots" id="dots"><?= str_repeat('<span></span>', count($testi)) ?></div>
<?php if (hidup('testimoni')): ?>    <div class="more"><a href="<?= u('testimoni') ?>" class="btn-o">Semua Testimoni <svg class="ic"><use href="#chev"/></svg></a></div><?php endif; ?>
  </section>

<?php footer_html(true); ?>
</div>

<a class="wa-fab" href="<?= h(wa_link()) ?>" target="_blank" rel="noopener" aria-label="Tanya kami di WhatsApp"><svg><use href="#wa"/></svg><span class="wa-tip">Tanya kami di WhatsApp</span></a>
<div class="drawer" id="dr" onclick="if(event.target===this)this.classList.remove('open')">
  <nav><?= menu_drawer(false, false) ?></nav>
</div>

<script>window.JJT_DATA = <?= data_js(false) ?>; window.PAKEJ_ON = <?= $pakejOn ? 'true' : 'false' ?>;</script>
<script src="<?= u('assets/app.js') ?>?v=<?= ASSET_V ?>"></script>
<script>
// Jenis trip → tukar senarai destinasi (tanpa muat semula page)
(function(){
  const tbs=[...document.querySelectorAll('.tb')];
  const grid=document.getElementById('destGrid'), semua=document.getElementById('destAll'), tl=document.getElementById('tl'), sub=document.getElementById('destSub');
  const sec=document.getElementById('destinasi');
  let cur=(tbs.find(t=>t.classList.contains('on'))||{}).dataset?.j;
  function renderDest(id){
    const j=JJT.jenis(id), ps=JJT.PAKEJ.filter(p=>p.jenis===id);
    const list=JJT.DESTINASI.map(d=>{const x=ps.filter(p=>p.dest===d.id);return {d,n:x.length,min:x.length?Math.min(...x.map(p=>p.harga)):0};})
      .filter(x=>x.n&&x.d.utama!==false).sort((a,b)=>!!b.d.img-!!a.d.img).slice(0,9);
    grid.classList.add('fade');
    setTimeout(()=>{
      grid.innerHTML=list.map(({d,n,min})=>`<a href="${PAKEJ_ON?JJT.url('destinasi/'+d.id)+'?jenis='+id:waLink('Assalamualaikum JJT, saya berminat dengan pakej '+j.nama+' ke '+(d.pendek||d.nama)+'.')}"${PAKEJ_ON?'':' target="_blank" rel="noopener"'} class="d${d.img?'':' noimg'}"${d.img?` style="background-image:url(${d.img})"`:''}>${d.img?'':'<span class="ph">Foto akan datang</span>'}<div class="d-l"><svg class="pin"><use href="#pin"/></svg><b>${esc(d.pendek||d.nama)}<span class="dm">${n} pakej · <span style="white-space:nowrap">dari ${JJT.rm(min)}</span></span></b><span class="arr fill">${ic('chev')}</span></div></a>`).join('');
      grid.classList.remove('fade');
    },160);
    tl.textContent=j.nama+' Anda';
    sub.textContent=`${j.ringkas} ${list.length} destinasi pilihan untuk ${j.nama}.`;
    if(semua) semua.href=JJT.url('pakej?jenis='+id);
  }
  /* Series Trip: jadual Series Departure ikut bulan (tarikh tetap) */
  const srs=document.getElementById('srs'), norm=document.getElementById('destNorm');
  const BLN=['Jan','Feb','Mac','Apr','Mei','Jun','Jul','Ogos','Sep','Okt','Nov','Dis'], BLNP=['Januari','Februari','Mac','April','Mei','Jun','Julai','Ogos','September','Oktober','November','Disember'];
  const esc=s=>String(s==null?'':s).replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
  const rm=n=>'RM '+Number(n).toLocaleString('en-US');
  const tgl=s=>{const [y,m,d]=s.split('-').map(Number);return new Date(Date.UTC(y,m-1,d));};
  const julat=(a,b)=>{const A=[a.getUTCDate(),BLN[a.getUTCMonth()],a.getUTCFullYear()],B=[b.getUTCDate(),BLN[b.getUTCMonth()],b.getUTCFullYear()];
    return A[2]!==B[2]?`${A.join(' ')} – ${B.join(' ')}`:A[1]!==B[1]?`${A[0]} ${A[1]} – ${B.join(' ')}`:`${A[0]} – ${B.join(' ')}`;};
  let srsKey=null;
  function bulanList(){const n=new Date(),o=[];for(let i=0;i<12;i++){const d=new Date(n.getFullYear(),n.getMonth()+i,1);o.push(d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0'));}return o;}
  function berlepas(){const o=[];JJT.PAKEJ.filter(p=>p.jenis==='series'&&Array.isArray(p.tarikh)).forEach(p=>p.tarikh.forEach(t=>{if(!t||!t.t)return;const a=tgl(t.t),b=new Date(a.getTime()+Math.max(0,(p.hari||1)-1)*864e5);o.push({p,t,a,b,key:t.t.slice(0,7)});}));return o.sort((x,y)=>x.a-y.a);}
  function renderSeries(key){
    const all=berlepas(), months=bulanList();
    if(!key){key=months.find(m=>all.some(x=>x.key===m))||months[0];}
    srsKey=key;const [y,m]=key.split('-').map(Number), ls=all.filter(x=>x.key===key);
    document.getElementById('srsT').innerHTML=`${BLNP[m-1]} <em>${y}</em>`;
    document.getElementById('srsSub').innerHTML=ls.length?(()=>{const nd=new Set(ls.map(x=>x.p.dest)).size;return nd===ls.length?`${nd} destinasi dengan tarikh perjalanan yang telah ditetapkan.`:`${ls.length} tarikh perjalanan ke ${nd} destinasi yang telah ditetapkan.`;})():`Tiada series departure untuk bulan ini. <a href="${waLink('Assalamualaikum JJT, saya ingin bertanya tentang Series Trip untuk bulan '+BLNP[m-1]+' '+y+'.')}" target="_blank" rel="noopener">Tanya kami di WhatsApp</a>.`;
    document.getElementById('srsM').innerHTML=months.map(k=>{const mm=+k.slice(5),ada=all.some(x=>x.key===k);return `<button type="button" role="tab" aria-selected="${k===key}" class="${k===key?'on':''}${ada?'':' kosong'}" data-k="${k}" title="${BLNP[mm-1]} ${k.slice(0,4)}">${BLN[mm-1]}</button>`;}).join('');
    document.getElementById('srsG').innerHTML=ls.map(({p,t,a,b})=>{const d=JJT.dest(p.dest)||{},img=p.img||d.img,penuh=t.s==='penuh',href=JJT.url('pakej/'+p.id)+'?t='+t.t;
      return `<article class="srs-c${penuh?' penuh':''}"><a class="srs-img${img?'':' noimg'}" href="${href}"${img?` style="background-image:url(${img})"`:''}><span class="srs-bdg">${penuh?'Penuh':'Series Departure'}</span><div class="srs-l"><svg class="pin"><use href="#pin"/></svg><div><b>${esc(d.pendek||d.nama||p.nama)}</b><small>${esc((p.laluan||[]).join(' · '))}</small></div></div></a>
        <div class="srs-b"><div class="srs-f"><div><svg class="ic"><use href="#cal"/></svg><span><small>Tarikh Perjalanan</small><b>${julat(a,b)}</b></span></div><div><svg class="ic"><use href="#clock"/></svg><span><small>Tempoh</small><b>${p.hari} Hari ${p.malam} Malam</b></span></div></div>
        <div class="srs-p"><div><small>Dari</small>${p.asal?`<s>${rm(p.asal)}</s>`:''}<b>${rm(p.harga)}</b><i>seorang</i></div><a class="srs-btn" href="${href}">Lihat Pakej <svg class="ic"><use href="#chev"/></svg></a></div></div></article>`;}).join('');
  }
  if(srs) document.getElementById('srsM').addEventListener('click',e=>{const b=e.target.closest('button[data-k]');if(b&&b.dataset.k!==srsKey)renderSeries(b.dataset.k);});
  function tunjuk(id){const s=id==='series'&&srs&&PAKEJ_ON;if(srs){srs.hidden=!s;norm.hidden=s;}if(s)renderSeries(srsKey);else renderDest(id);}
  if(cur==='series') tunjuk('series');
  tbs.forEach(t=>t.addEventListener('click',e=>{
    e.preventDefault();
    const id=t.dataset.j;
    if(cur!==id){ tbs.forEach(x=>x.classList.toggle('on',x===t)); tunjuk(id); cur=id; }
    setTimeout(()=>{const y=sec.getBoundingClientRect().top+scrollY-(document.querySelector('.hd').offsetHeight+8);scrollTo({top:y,behavior:'smooth'});},60);
  }));
})();
const tr=document.getElementById('tr'),dots=[...document.querySelectorAll('#dots span')];
if(dots[0]) dots[0].classList.add('on');
tr.addEventListener('scroll',()=>{const w=tr.firstElementChild.getBoundingClientRect().width+10;const i=Math.min(dots.length-1,Math.round(tr.scrollLeft/w));dots.forEach((d,j)=>d.classList.toggle('on',i===j));});
</script>
</body>
</html>
