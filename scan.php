<?php require __DIR__ . '/functions.php'; header_html('Pindai QR Pemilih'); ?>
<div class="center-panel"><div class="eyebrow">TAHAP 1 / PINDAI</div><h1>Pindai QR Warga</h1><p>Arahkan kamera ke QR yang diberikan panitia.</p><div id="reader"></div><p id="scan-note" class="muted">Kamera membutuhkan HTTPS atau localhost. Izinkan akses kamera saat diminta.</p>
<form action="surat_suara.php" method="get" class="manual"><label>Atau masukkan kode QR<input id="code" name="code" placeholder="Masukkan kode dari kartu QR" required autocomplete="off"></label><button type="submit" class="btn">Lanjut Memilih</button></form></div>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
if(window.Html5Qrcode){
  let scanned=false;
  const scanner = new Html5Qrcode('reader');
  scanner.start({facingMode:'environment'},{fps:10,qrbox:{width:220,height:220}},txt=>{
    if(scanned)return; scanned=true;
    location.href='surat_suara.php?code='+encodeURIComponent(txt.trim());
  },()=>{}).catch(err=>{document.getElementById('scan-note').textContent='Kamera tidak dapat dibuka. Gunakan HTTPS, berikan izin kamera, atau input kode manual.';});
}else{document.getElementById('scan-note').textContent='Library pemindai tidak dapat dimuat. Gunakan kode manual atau periksa koneksi internet.';}
</script>
<?php footer_html(); ?>
