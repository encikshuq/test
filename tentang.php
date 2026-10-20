<?php
require __DIR__ . '/inc/layout.php';
pastikan_hidup('tentang', 'Tentang Kami');
$X = kand('tentangX');
/* Teks dari dashboard: setiap baris = satu perenggan, **teks** = tebal */
$para = fn($t, $cls = '') => implode('', array_map(fn($p) => '<p' . ($cls ? ' class="' . $cls . '"' : '') . '>' . preg_replace('/\*\*(.+?)\*\*/u', '<b>$1</b>', h($p)) . '</p>', array_filter(array_map('trim', explode("\n", (string)$t)), 'strlen')));
$im = fn($p) => $p ? h(img($p)) : '';
$STAT_IC = ['cal', 'pin', 'users', 'star'];
$KEN_IC = ['f-group', 'f-mosque', 'users', 'cal'];
page_head([
    'title' => 'Tentang Kami — Jalan Jalan Travel',
    'desc' => 'Jalan Jalan Travel & Tours International Sdn Bhd — syarikat pelancongan milik Bumiputera Muslim sejak 2016, berdaftar dengan SSM dan berlesen MOTAC. Percutian mesra Muslim dan Private Trip ke seluruh dunia.',
    'path' => 'tentang',
    'page' => 'tentang',
    'body_class' => 'no-cta',
]);
?>

    <section class="tk-hero"<?= $X['hero_img'] ? ' style="--tkbg:url(\'' . $im($X['hero_img']) . '\')"' : '' ?>>
      <div class="tk-hero-t">
        <div class="eb">Tentang Kami</div>
        <h1>Tentang <em><?= h($X['hero_tajuk']) ?></em></h1>
        <p class="tk-slogan"><?= h($X['slogan']) ?></p>
        <div class="tk-hero-p"><?= $para($X['hero_ayat']) ?></div>
      </div>
    </section>

    <div class="tk-stat">
<?php foreach (array_slice((array)$X['stat'], 0, 4) as $i => $s): ?>
      <div><span class="tk-si"><?= ic($STAT_IC[$i] ?? 'spark') ?></span><b><?= h($s['v'] ?? '') ?></b><small><?= h($s['l'] ?? '') ?></small></div>
<?php endforeach; ?>
    </div>

    <section class="sx tk-kisah">
      <div class="tk-kisah-t">
        <div class="eb">Kisah Kami</div>
        <h2 class="tt"><?= h($X['kisah_tajuk']) ?></h2>
        <div class="tk-p"><?= $para($X['kisah_ayat']) ?></div>
        <blockquote class="tk-quote"><span class="tk-q">“</span><div><?= $para($X['petikan']) ?><p class="tk-qa"><b><?= h($X['petikan_akhir']) ?></b> <em><?= h($X['slogan']) ?>.</em></p></div></blockquote>
      </div>
<?php if ($X['foto1'] || $X['foto2']): ?>
      <div class="tk-polar" aria-hidden="true">
<?php if ($X['foto1']): ?>        <span class="pf a"><img src="<?= $im($X['foto1']) ?>" alt="" loading="lazy"></span><?php endif; ?>
<?php if ($X['foto2']): ?>        <span class="pf b"><img src="<?= $im($X['foto2']) ?>" alt="" loading="lazy"></span><?php endif; ?>
<?php if ($X['foto_nota']): ?>        <span class="tk-nota"><?= h($X['foto_nota']) ?><svg viewBox="0 0 60 40"><path d="M4 34C20 32 36 22 48 8M48 8l-10 2M48 8l-1 10"/></svg></span><?php endif; ?>
      </div>
<?php endif; ?>
    </section>

    <section class="sx" id="kenapa">
      <div class="eb">Kenapa Pilih JJT?</div>
      <h2 class="tt">Lebih Daripada Sekadar <em>Percutian Biasa</em></h2>
      <div class="tk-why">
<?php foreach (array_slice((array)$X['kenapa'], 0, 4) as $i => $k): ?>
        <div class="card"><span class="tk-wi"><svg class="ic"><use href="#<?= $KEN_IC[$i] ?? 'spark' ?>"/></svg></span><div><b><?= h($k['t'] ?? '') ?></b><p><?= h($k['a'] ?? '') ?></p></div></div>
<?php endforeach; ?>
      </div>
    </section>

    <section class="sx tk-team<?= $X['pasukan_img'] ? '' : ' noimg' ?>">
      <div>
        <div class="eb">Kenali Team Kami</div>
        <h2 class="tt"><?= h($X['pasukan_tajuk']) ?></h2>
        <div class="tk-p"><?= $para($X['pasukan_ayat']) ?></div>
      </div>
<?php if ($X['pasukan_img']): ?>      <img class="tk-team-img" src="<?= $im($X['pasukan_img']) ?>" alt="Team Jalan Jalan Travel" loading="lazy"><?php endif; ?>
    </section>

    <section class="sx" id="lesen">
      <div class="eb">Lesen &amp; Kredibiliti</div>
      <h2 class="tt">Berdaftar &amp; <em>Dipercayai</em></h2>
      <div class="tk-p"><?= $para($X['lesen_ayat']) ?></div>
      <div class="tk-lesen">
<?php if ($X['kpk']): ?>        <div class="card"><span class="tk-li"><?= ic('badge') ?></span><small>No. Lesen MOTAC</small><b><?= h($X['kpk']) ?></b><span>Kementerian Pelancongan, Seni dan Budaya Malaysia</span></div><?php endif; ?>
<?php if ($X['ssm']): ?>        <div class="card"><span class="tk-li"><?= ic('bldg') ?></span><small>No. Pendaftaran SSM</small><b><?= h($X['ssm']) ?></b><span>Jalan Jalan Travel &amp; Tours International Sdn Bhd</span></div><?php endif; ?>
<?php if ($X['matta']): ?>        <div class="card"><span class="tk-li"><?= ic('shield') ?></span><small>Keahlian MATTA</small><b><?= h($X['matta']) ?></b><span>Malaysian Association of Tour and Travel Agents</span></div><?php endif; ?>
      </div>
<?php if ($X['alamat']): ?>
      <div class="card tk-alamat"><span class="ci or"><?= ic('pin') ?></span><div><b>Alamat Pejabat</b><p><?= nl2br(h($X['alamat'])) ?></p></div></div>
<?php endif; ?>
<?php if ($X['lesen_url']): ?>
      <div class="tk-c"><a class="btn tk-btn-ol" href="<?= h(img($X['lesen_url'])) ?>" target="_blank" rel="noopener">Lihat Lesen &amp; Sijil <?= ic('chev') ?></a></div>
<?php endif; ?>
    </section>

    <section class="sx" id="kenangan">
      <div class="eb">Kenangan Bersama Pelanggan</div>
      <h2 class="tt">Setiap Perjalanan <em>Ada Ceritanya</em></h2>
      <div class="tk-p"><?= $para($X['kenangan_ayat']) ?></div>
<?php $gal = array_values(array_filter((array)$X['galeri'])); if ($gal): ?>
      <div class="tk-gal"><?php foreach (array_slice($gal, 0, 8) as $g): ?><img src="<?= $im($g) ?>" alt="Kenangan pelanggan Jalan Jalan Travel" loading="lazy"><?php endforeach; ?></div>
<?php endif; ?>
<?php if (hidup('testimoni')): ?>
      <div class="tk-c"><a class="btn tk-btn-ol" href="<?= u('testimoni') ?>">Lihat Feedback Kami <?= ic('chev') ?></a></div>
<?php endif; ?>
    </section>

    <section class="tk-cta"<?= $X['cta_img'] ? ' style="--tkbg:url(\'' . $im($X['cta_img']) . '\')"' : '' ?>>
      <div class="tk-cta-t">
        <h2><?= preg_replace('/(Berbeza)/u', '<em>$1</em>', h($X['cta_tajuk'])) ?></h2>
        <div><?= $para($X['cta_ayat']) ?></div>
        <div class="tk-cta-b">
          <a class="btn btn-or" href="<?= h(wa_link('Assalamualaikum JJT, saya ingin bertanya tentang pakej percutian. Boleh bantu cadangkan pakej yang sesuai?')) ?>" target="_blank" rel="noopener"><svg class="w"><use href="#wa"/></svg>Tanya Sekarang</a>
<?php if (hidup('pakej')): ?>          <a class="btn tk-btn-w" href="<?= u('pakej') ?>">Lihat Semua Pakej <?= ic('chev') ?></a><?php endif; ?>
        </div>
      </div>
    </section>
<?php page_end();
