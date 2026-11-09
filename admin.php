<?php
require __DIR__ . '/functions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $name = trim((string)($_POST['name'] ?? ''));
    if ($name === '' || mb_strlen($name) > 100) { flash('Nama harus 1–100 karakter.'); redirect('admin.php'); }
    $code = strtoupper(bin2hex(random_bytes(12)));
    with_state(static function(array &$s) use ($name,$code): void {
        $s['voters'][$code] = ['name' => $name, 'used' => false, 'created_at' => date('c')];
    });
    flash('QR untuk ' . $name . ' berhasil dibuat.'); redirect('admin.php');
}
$voters = with_state(static fn(array &$s): array => $s['voters']);
header_html('Buat QR Warga');
?>
<div class="page-heading"><div><div class="eyebrow">HALAMAN PANITIA DEMO</div><h1>Generate QR Berdasarkan Nama</h1><p>Buat tiket QR untuk peserta simulasi, lalu cetak atau bagikan.</p></div><a href="hasil.php" class="btn secondary">Lihat Hasil</a></div>
<?php if ($m = take_flash()): ?><div class="notice"><?=e($m)?></div><?php endif; ?>
<form class="panel form-row" action="admin.php" method="post"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><label>Nama warga<input name="name" required maxlength="100" placeholder="Contoh: Budi Santoso"></label><button class="btn" type="submit">+ Buat QR</button></form>
<div class="notice warning">Demo ini belum memakai login admin. Jangan unggah ke internet publik tanpa autentikasi dan pengamanan tambahan. Kode QR adalah akses untuk memberikan suara.</div>
<h2>Daftar QR warga (<?=count($voters)?>)</h2>
<?php if (!$voters): ?><p>Belum ada data warga. Tambahkan nama di atas.</p><?php else: ?>
<div class="qr-grid">
<?php foreach (array_reverse($voters, true) as $code => $voter): ?>
<div class="qr-card"><div class="qr" data-code="<?=e($code)?>"></div><strong><?=e($voter['name'])?></strong><div class="token"><?=e($code)?></div><span class="status <?= $voter['used'] ? 'used' : '' ?>"><?= $voter['used'] ? 'Sudah memilih' : 'Belum memilih' ?></span></div>
<?php endforeach; ?>
</div>
<div class="actions print-hide"><button onclick="window.print()" class="btn">Cetak Lembar QR</button></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
document.querySelectorAll('.qr').forEach(el => {

    if (!window.QRCode) {
        el.textContent = 'Gagal membuat QR Code';
        return;
    }

    const code = el.dataset.code;

    // Buat URL langsung menuju surat suara
    const url = new URL(
        'surat_suara.php',
        window.location.href
    );

    url.searchParams.set('code', code);

    // Bersihkan QR lama
    el.innerHTML = '';

    // Generate QR yang berisi URL website
    new QRCode(el, {
        text: url.toString(),
        width: 180,
        height: 180,
        correctLevel: QRCode.CorrectLevel.M
    });

});
</script>
<?php endif; ?>
<?php footer_html(); ?>
