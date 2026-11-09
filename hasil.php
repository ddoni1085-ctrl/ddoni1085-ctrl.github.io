<?php
require __DIR__ . '/functions.php';
$state = with_state(static fn(array &$s): array => $s);
$counts = array_fill_keys(array_keys(CANDIDATES), 0);
foreach ($state['votes'] as $vote) { if (isset($counts[$vote['candidate_id']])) $counts[$vote['candidate_id']]++; }
$total = array_sum($counts);
$registered = count($state['voters']);
header_html('Hasil Simulasi');
?>
<div class="page-heading"><div><div class="eyebrow">REKAP REAL-TIME DEMO</div><h1>Hasil Simulasi Pemilihan</h1><p>Perolehan suara per calon, tanpa menampilkan hubungan antara nama warga dan kandidat.</p></div><a class="btn secondary" href="scan.php">Scan Berikutnya</a></div>
<div class="stats"><div><span>QR diterbitkan</span><strong><?=$registered?></strong></div><div><span>Sudah memilih</span><strong><?=$total?></strong></div><div><span>Belum memilih</span><strong><?=max(0, $registered-$total)?></strong></div></div>
<div class="results">
<?php foreach(CANDIDATES as $id=>$c): $votes=$counts[$id]; $pct=$total ? round($votes*100/$total,1) : 0; ?>
<div class="result-row"><img src="<?=e($c['photo'])?>" alt=""><div class="result-info"><div class="result-label"><b><?=$id?>. <?=e($c['name'])?></b><b><?=$votes?> suara (<?=$pct?>%)</b></div><div class="bar"><div style="width:<?=$pct?>%"></div></div></div></div>
<?php endforeach; ?>
</div>
<h2>Daftar peserta (status saja)</h2>
<div class="table-wrap"><table><thead><tr><th>No.</th><th>Nama</th><th>Status</th></tr></thead><tbody>
<?php $n=0; foreach($state['voters'] as $v): ?><tr><td><?=++$n?></td><td><?=e($v['name'])?></td><td><?=$v['used'] ? 'Sudah memilih' : 'Belum memilih'?></td></tr><?php endforeach; ?>
<?php if(!$n): ?><tr><td colspan="3">Belum ada peserta</td></tr><?php endif; ?>
</tbody></table></div>
<?php footer_html(); ?>
