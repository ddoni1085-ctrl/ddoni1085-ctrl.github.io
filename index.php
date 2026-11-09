<?php
require __DIR__ . '/functions.php';
header_html(APP_TITLE);
?>
<section class="hero"><div class="eyebrow">DEMO UNTUK WARGA</div><h1>Simulasi Pemilihan Kepala Desa Digital</h1><p>Kenali alur pemungutan suara dari pemindaian QR sampai penghitungan hasil.</p><div class="actions"><a class="btn" href="scan.php">Mulai Pindai QR</a><a class="btn secondary" href="admin.php">Buat QR Warga</a></div></section>
<div class="steps"><div><b>1. Terima QR</b><p>Panitia membuat QR berdasarkan nama warga.</p></div><div><b>2. Pindai QR</b><p>Gunakan kamera HP atau masukkan kode secara manual.</p></div><div><b>3. Pilih & konfirmasi</b><p>Pilih foto calon lalu tekan tombol konfirmasi.</p></div><div><b>4. Lihat hasil</b><p>Satu QR hanya dapat memberikan satu suara.</p></div></div>
<?php footer_html(); ?>
