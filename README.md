# Simulasi Pilkades Digital — PHP Native

Simulasi edukasi, **bukan sistem pemilu resmi**. Tidak memakai Composer, framework, MySQL, atau terminal server. Data disimpan pada `data/state.json` dengan `flock` agar pemakaian QR satu kali saja, termasuk saat dua permintaan masuk bersamaan.

## Cara menjalankan
1. Upload seluruh isi folder `simulasi_pilkades` ke hosting PHP 8.0+ (misalnya `public_html/simulasi_pilkades/`).
2. Pastikan PHP bisa menulis folder `data/` dan file `state.json` (izin folder lazimnya 755/775, sesuai konfigurasi hosting). `data/.htaccess` memblokir akses HTTP pada Apache. Pada Nginx, **atur deny location /data/** secara manual, atau pindahkan `data` ke luar document root lalu ubah `STORE_PATH` dalam `config.php`.
3. Buka `.../simulasi_pilkades/admin.php`, masukkan nama warga satu per satu untuk membuat QR.
4. Cetak kartu QR dari tombol **Cetak Lembar QR**. Pemindaian memakai `scan.php` dan kamera HP (perlu HTTPS/localhost).
5. Pilih foto calon di `surat_suara.php`, konfirmasi, lalu buka `hasil.php` untuk melihat rekap dan status warga.

QR berisi kode acak unik. Nama warga dipasangkan dengan kode di server, jadi saat QR dipindai nama warga tampil. `votes` menyimpan ID calon saja tanpa nama ataupun QR, sehingga hasil per nama dibatasi pada status sudah/belum memilih dan **tidak membuka pilihan rahasia**.

## Ubah identitas desa & calon
Edit `config.php`. Foto tersimpan pada `assets/img/calon1.jpg` sampai `calon5.jpg` (saat ini memakai *crop* dari gambar contoh yang diunggah, kualitas aslinya rendah). Ganti kelima gambar dengan foto berizin dan resolusi lebih tinggi.

## Ketergantungan internet
Pembuatan QR memakai `qrcodejs` dari CDN; kamera pemindai memakai `html5-qrcode` dari CDN. Tidak perlu install paket. Untuk demo **offline**, unduh kedua library JavaScript ke `assets/js/` lalu ganti tag `<script src=...>` di `admin.php`/`scan.php` ke file lokal.

## Penting: batas keamanan
- Halaman `admin.php` dan `hasil.php` **belum dilindungi login**: hanya untuk demo lokal/internal. Untuk penggunaan nyata, wajib login admin, HTTPS, rate limiting, pengamanan backup & berkas data, audit independen, verifikasi DPT, anonimitas suara, dan prosedur pemulihan.
- Kode QR adalah **bearer credential**: siapa pun yang mendapat salinan kode sebelum dipakai bisa menggunakannya. Panitia tetap perlu mengecek identitas warga.
- Jangan pakai aplikasi ini sebagai penyelenggaraan Pilkades resmi tanpa pengujian dan persetujuan berwenang.
- Saat mendemokan pada warga, jelaskan bahwa suara disimpan agregat dan satu QR hanya digunakan sekali.
