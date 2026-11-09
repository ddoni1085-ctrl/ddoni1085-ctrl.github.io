<?php
require __DIR__ . '/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Metode tidak diizinkan'); }
check_csrf();
$code = strtoupper(trim((string)($_POST['code'] ?? '')));
$id = filter_var($_POST['candidate_id'] ?? null, FILTER_VALIDATE_INT);
if (!preg_match('/^[A-F0-9]{24}$/', $code) || !isset(CANDIDATES[$id])) { http_response_code(400); exit('Data suara tidak valid.'); }
$result = with_state(static function(array &$s) use ($code,$id): string {
    if (!isset($s['voters'][$code])) { return 'invalid'; }
    if ($s['voters'][$code]['used']) { return 'used'; }
    $s['voters'][$code]['used'] = true;
    $s['voters'][$code]['voted_at'] = date('c');
    // Suara disimpan tanpa kode dan nama pemilih agar pilihan tetap rahasia.
    $s['votes'][] = ['candidate_id' => $id, 'at' => date('c')];
    return 'ok';
});
if ($result === 'ok') { redirect('sukses.php'); }
flash($result === 'used' ? 'QR sudah dipakai. Suara tidak dicatat dua kali.' : 'QR tidak valid.');
redirect('scan.php');
