
<?php
require __DIR__ . '/functions.php';

// ============================================
// VALIDASI QR CODE PEMILIH
// ============================================

$code = strtoupper(trim((string) ($_GET['code'] ?? '')));

$voter = preg_match('/^[A-F0-9]{24}$/', $code)
    ? voter_by_code($code)
    : null;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Surat Suara Digital - <?= e(APP_VILLAGE) ?></title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #edf0f4;
            color: #111;
            min-height: 100vh;
        }

        button {
            font-family: inherit;
        }

        /* =====================================
           SURAT SUARA
        ===================================== */

        .surat-wrapper {
            width: 100%;
            max-width: 1280px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0,0,0,.12);
        }

        /* HEADER SURAT SUARA */

        .surat-header {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 195px;
            padding: 18px 90px;
        }

        .logo-kabupaten {
            position: absolute;
            top: 20px;
            left: 25px;
        }

        .logo-kabupaten img {
            width: 65px;
            height: 75px;
            object-fit: contain;
        }

        .judul-surat {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .judul-surat h1 {
            font-size: 22px;
            font-weight: 800;
        }

        .judul-surat h2,
        .judul-surat h3 {
            font-size: 16px;
            font-weight: 800;
        }

        .judul-surat .tahun {
            font-size: 15px;
            font-weight: 800;
        }

        .label-simulasi {
            color: #d63434;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        /* INFORMASI PEMILIH */

        .informasi-pemilih {
            text-align: center;
            font-size: 13px;
            margin: 0 12px 12px;
            color: #555;
        }

        .informasi-pemilih strong {
            color: #111;
        }

        /* PETUNJUK */

        .petunjuk {
            text-align: center;
            font-size: 14px;
            margin-bottom: 18px;
            padding: 0 12px;
        }

        /* =====================================
           DAFTAR FOTO CALON
        ===================================== */

        .candidate-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
            padding: 0 10px;
        }

        .candidate {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            min-width: 0;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            cursor: pointer;
            transition: .2s ease;
            text-align: center;
            color: #111;
        }

        .candidate:hover {
            border-color: #0096ef;
            box-shadow: 0 0 0 3px rgba(0,150,239,.18);
            transform: translateY(-2px);
        }

        /* Pilihan sementara, belum tersimpan sebagai suara */
        .candidate.selected {
            border: 4px solid #e02323;
            box-shadow: 0 0 0 2px rgba(224, 35, 35, .15);
            transform: none;
        }

        .candidate.selected .nomor-calon {
            background: #e02323;
        }

        .candidate.selected .nama-calon {
            color: #c91e1e;
        }

        .candidate:focus-visible {
            outline: 3px solid #0096ef;
            outline-offset: 2px;
        }

        .foto-wrapper {
            position: relative;
            width: 100%;
            aspect-ratio: 4 / 4.5;
            background: #e1ecf2;
            overflow: hidden;
        }

        .foto-wrapper img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
        }

        /* NOMOR URUT */

        .nomor-calon {
            position: absolute;
            top: 6px;
            left: 6px;
            min-width: 25px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0798ed;
            color: #fff;
            font-size: 15px;
            font-weight: bold;
            border-radius: 3px;
            z-index: 2;
        }

        /* NAMA CALON */

        .nama-calon {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 55px;
            padding: 8px 5px;
            font-size: 14px;
            font-weight: bold;
            line-height: 1.3;
            overflow-wrap: anywhere;
        }

        /* =====================================
           BANNER MERAH
        ===================================== */

        .banner-bawah {
            display: block;
            width: 100%;
            border: 0;
            cursor: pointer;
            font-family: inherit;
            margin-top: 10px;
            background: #f25454;
            text-align: center;
            padding: 17px 12px;
            color: white;
            font-size: 16px;
            font-weight: bold;
        }

        .banner-bawah:disabled {
            background: #999;
            cursor: not-allowed;
        }

        .banner-bawah:not(:disabled):hover {
            background: #da3b3b;
        }

        .footer-simulasi {
            text-align: center;
            padding: 12px;
            font-size: 11px;
            color: #777;
        }

        /* =====================================
           MODAL KONFIRMASI
        ===================================== */

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.65);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
            z-index: 99999;
        }

        .modal-backdrop[hidden] {
            display: none !important;
        }

        .modal {
            background: #fff;
            width: 100%;
            max-width: 650px;
            border-radius: 8px;
            padding: 22px;
            box-shadow: 0 10px 35px rgba(0,0,0,.3);
            text-align: center;
            animation: modalMasuk .2s ease;
        }

        @keyframes modalMasuk {
            from {
                opacity: 0;
                transform: scale(.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .modal h2 {
            font-size: 18px;
            font-weight: bold;
            text-align: left;
            margin-bottom: 6px;
        }

        .modal .pertanyaan {
            color: #db4545;
            font-size: 14px;
            text-align: left;
            margin-bottom: 18px;
        }

        .pilihan-konfirmasi {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            margin-bottom: 25px;
        }

        .pilihan-konfirmasi img {
            width: 125px;
            height: 145px;
            object-fit: cover;
            object-position: top;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .pilihan-konfirmasi strong {
            font-size: 14px;
        }

        /* TOMBOL MODAL */

        .modal-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 15px;
        }

        .btn {
            border: 0;
            border-radius: 5px;
            padding: 13px 18px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            background: #0797eb;
            color: white;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .btn:hover {
            filter: brightness(.94);
        }

        .btn:disabled {
            opacity: .65;
            cursor: not-allowed;
        }

        .btn.secondary {
            background: #e8e8e8;
            color: #333;
        }

        /* =====================================
           HALAMAN QR ERROR / SUDAH DIGUNAKAN
        ===================================== */

        .center-panel {
            width: 92%;
            max-width: 550px;
            margin: 90px auto;
            background: #fff;
            border-radius: 10px;
            padding: 35px 25px;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0,0,0,.1);
        }

        .center-panel h1 {
            margin-bottom: 15px;
            font-size: 25px;
        }

        .center-panel p {
            line-height: 1.6;
            margin-bottom: 25px;
            color: #555;
        }

        /* =====================================
           RESPONSIVE TABLET
        ===================================== */

        @media (max-width: 900px) {
            .candidate-grid {
                gap: 5px;
            }

            .nama-calon {
                font-size: 11px;
            }

            .surat-header {
                min-height: 170px;
            }
        }

        /* =====================================
           RESPONSIVE HP
        ===================================== */

        @media (max-width: 600px) {

            body {
                background: #fff;
            }

            .surat-wrapper {
                margin: 0;
                border-radius: 0;
                box-shadow: none;
            }

            .surat-header {
                min-height: 160px;
                padding: 20px 45px;
            }

            .logo-kabupaten {
                top: 15px;
                left: 8px;
            }

            .logo-kabupaten img {
                width: 34px;
                height: 45px;
            }

            .judul-surat h1 {
                font-size: 19px;
            }

            .judul-surat h2,
            .judul-surat h3 {
                font-size: 12px;
            }

            .judul-surat .tahun {
                font-size: 12px;
            }

            .label-simulasi {
                font-size: 9px;
            }

            .petunjuk {
                font-size: 12px;
            }

            .candidate-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 8px;
                padding: 0 8px;
            }

            .nama-calon {
                font-size: 12px;
                min-height: 50px;
            }

            .foto-wrapper {
                aspect-ratio: 4 / 4.5;
            }

            .nomor-calon {
                font-size: 13px;
                min-width: 23px;
                height: 25px;
            }

            .banner-bawah {
                font-size: 13px;
                padding: 15px;
            }

            .modal {
                padding: 18px;
            }

            .modal h2 {
                font-size: 16px;
            }

            .pilihan-konfirmasi img {
                width: 115px;
                height: 135px;
            }

            .modal-actions {
                gap: 8px;
            }

            .btn {
                font-size: 13px;
                padding: 12px;
            }
        }
    </style>
</head>

<body>

<?php if (!$voter): ?>

    <!-- =====================================
         QR TIDAK TERDAFTAR
    ===================================== -->

    <div class="center-panel">
        <h1>QR Tidak Ditemukan</h1>

        <p>
            QR Code yang dipindai tidak terdaftar
            dalam sistem simulasi pemilihan.
            Silakan hubungi panitia.
        </p>

        <a class="btn" href="scan.php">
            Pindai Ulang
        </a>
    </div>

<?php elseif ($voter['used']): ?>

    <!-- =====================================
         QR SUDAH DIPAKAI
    ===================================== -->

    <div class="center-panel">
        <h1>QR Sudah Digunakan</h1>

        <p>
            QR Code atas nama
            <strong><?= e($voter['name']) ?></strong>
            sudah tercatat mengikuti simulasi pemilihan.

            <br><br>

            Setiap QR Code hanya dapat digunakan
            satu kali.
        </p>

        <a class="btn" href="hasil.php">
            Lihat Hasil
        </a>
    </div>

<?php else: ?>

    <!-- =====================================
         HALAMAN SURAT SUARA
    ===================================== -->

    <main class="surat-wrapper">

        <!-- HEADER -->
        <header class="surat-header">

            <div class="logo-kabupaten">
                <img
                    src="assets/img/"
                    alt="Logo Kabupaten"
                    onerror="this.style.display='none'"
                >
            </div>

            <div class="judul-surat">

                <div class="label-simulasi">
                    SIMULASI PEMILIHAN DIGITAL
                </div>

                <h1>SURAT SUARA</h1>

                <h2>PEMILIHAN KEPALA DESA</h2>

                <h3>
                    <?= strtoupper(e(APP_VILLAGE)) ?>
                </h3>

                <div class="tahun">
                    TAHUN 2026
                </div>

            </div>

        </header>

        <!-- NAMA PEMILIH -->
        <div class="informasi-pemilih">
            Pemilih:
            <strong>
                <?= e($voter['name']) ?>
            </strong>
        </div>

        <!-- PETUNJUK -->
        <p class="petunjuk">
            Silakan pilih salah satu foto calon kepala desa.
        </p>

        <!-- FOTO CALON -->
        <div class="candidate-grid">

            <?php foreach (CANDIDATES as $id => $c): ?>

                <button
                    type="button"
                    class="candidate"
                    data-id="<?= (int) $id ?>"
                    data-name="<?= e($c['name']) ?>"
                    data-photo="<?= e($c['photo']) ?>"
                    aria-pressed="false"
                >

                    <div class="foto-wrapper">

                        <span class="nomor-calon">
                            <?= (int) $id ?>
                        </span>

                        <img
                            src="<?= e($c['photo']) ?>"
                            alt="Foto <?= e($c['name']) ?>"
                            loading="eager"
                        >

                    </div>

                    <div class="nama-calon">
                        <?= e($c['name']) ?>
                    </div>

                </button>

            <?php endforeach; ?>

        </div>

        <!-- TOMBOL LANJUT KONFIRMASI -->
        <button
            type="button"
            class="banner-bawah"
            id="btn-konfirmasi"
            disabled
        >
            Pilih Salah Satu Calon Terlebih Dahulu
        </button>

        <div class="footer-simulasi">
            SIMULASI PEMILIHAN KEPALA DESA DIGITAL
        </div>

    </main>

    <!-- =====================================
         MODAL KONFIRMASI
    ===================================== -->

    <div
        class="modal-backdrop"
        id="modal"
        hidden
    >

        <div
            class="modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-title"
        >

            <h2 id="modal-title">
                Konfirmasi Pemilihan
            </h2>

            <p class="pertanyaan">
                Apa Anda yakin akan memilih kandidat ini?
            </p>

            <div class="pilihan-konfirmasi">

                <img
                    id="chosen-photo"
                    src=""
                    alt="Foto calon terpilih"
                >

                <strong id="chosen-name"></strong>

            </div>

            <!-- FORM PEMILIHAN -->
            <form
                action="proses_suara.php"
                method="post"
                class="modal-actions"
                id="form-vote"
            >

                <input
                    type="hidden"
                    name="csrf"
                    value="<?= e(csrf()) ?>"
                >

                <input
                    type="hidden"
                    name="code"
                    value="<?= e($code) ?>"
                >

                <input
                    type="hidden"
                    name="candidate_id"
                    id="candidate-id"
                >

                <button
                    class="btn secondary"
                    type="button"
                    id="cancel"
                >
                    Ulangi
                </button>

                <button
                    class="btn"
                    type="submit"
                    id="submit-vote"
                >
                    Ya, Pilih
                </button>

            </form>

        </div>

    </div>

    <!-- =====================================
         JAVASCRIPT PEMILIHAN
    ===================================== -->

    <script>
        const modal = document.getElementById('modal');
        const chosenPhoto = document.getElementById('chosen-photo');
        const chosenName = document.getElementById('chosen-name');
        const candidateId = document.getElementById('candidate-id');
        const cancelButton = document.getElementById('cancel');
        const voteForm = document.getElementById('form-vote');
        const submitButton = document.getElementById('submit-vote');
        const confirmButton = document.getElementById('btn-konfirmasi');
        const candidates = document.querySelectorAll('.candidate');

        let selectedButton = null;

        // Menyentuh calon hanya menampilkan garis merah (belum menyimpan suara).
        candidates.forEach(button => {
            button.addEventListener('click', function() {
                candidates.forEach(item => {
                    item.classList.remove('selected');
                    item.setAttribute('aria-pressed', 'false');
                });

                selectedButton = button;
                button.classList.add('selected');
                button.setAttribute('aria-pressed', 'true');
                candidateId.value = button.dataset.id;

                confirmButton.disabled = false;
                confirmButton.textContent =
                    'Pilih Kandidat Nomor ' + button.dataset.id;
            });
        });

        // Modal baru dibuka ketika tombol merah ditekan.
        confirmButton.addEventListener('click', function() {
            if (!selectedButton || !candidateId.value) return;

            chosenPhoto.src = selectedButton.dataset.photo;
            chosenName.textContent =
                'Nomor ' + selectedButton.dataset.id +
                ' - ' + selectedButton.dataset.name;
            modal.hidden = false;
            cancelButton.focus();
        });

        // Ulangi menutup modal, namun pilihan sementara tetap diseleksi.
        function closeModal() {
            modal.hidden = true;
            confirmButton.focus();
        }

        cancelButton.addEventListener('click', closeModal);

        modal.addEventListener('click', function(event) {
            if (event.target === modal) closeModal();
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !modal.hidden) closeModal();
        });

        // Validasi sederhana pada browser dan cegah submit ganda.
        // Validasi suara yang sesungguhnya tetap dilakukan di proses_suara.php.
        voteForm.addEventListener('submit', function(event) {
            if (!selectedButton || !candidateId.value || modal.hidden) {
                event.preventDefault();
                return;
            }

            if (voteForm.dataset.submitted === '1') {
                event.preventDefault();
                return;
            }

            voteForm.dataset.submitted = '1';
            submitButton.disabled = true;
            submitButton.textContent = 'Memproses...';
        });
    </script>

<?php endif; ?>

</body>
</html>
