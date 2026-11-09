<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(24)); }
function check_csrf(): void {
    if (!hash_equals(csrf(), (string)($_POST['csrf'] ?? ''))) {
        http_response_code(403); exit('Token keamanan tidak valid. Muat ulang halaman.');
    }
}
function redirect(string $to): never { header('Location: ' . $to, true, 303); exit; }
function flash(string $message): void { $_SESSION['flash'] = $message; }
function take_flash(): string { $m = (string)($_SESSION['flash'] ?? ''); unset($_SESSION['flash']); return $m; }
/** Kunci eksklusif file dipakai pada seluruh operasi agar QR tidak bisa memilih dua kali. */
function with_state(callable $callback): mixed {
    if (!is_dir(dirname(STORE_PATH))) { mkdir(dirname(STORE_PATH), 0750, true); }
    $fp = fopen(STORE_PATH, 'c+');
    if (!$fp || !flock($fp, LOCK_EX)) { throw new RuntimeException('Penyimpanan tidak tersedia.'); }
    try {
        rewind($fp);
        $raw = stream_get_contents($fp);
        $state = ($raw !== '') ? json_decode($raw, true) : null;
        if (!is_array($state)) { $state = ['voters' => [], 'votes' => []]; }
        $before = json_encode($state);
        $result = $callback($state);
        if (json_encode($state) !== $before) {
            rewind($fp); ftruncate($fp, 0);
            fwrite($fp, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
            fflush($fp);
        }
        return $result;
    } finally { flock($fp, LOCK_UN); fclose($fp); }
}
function voter_by_code(string $code): ?array {
    return with_state(static fn(array &$s): ?array => $s['voters'][$code] ?? null);
}
function header_html(string $title): void {
    $title = e($title);
    echo <<<HTML
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{$title}</title><link rel="stylesheet" href="assets/style.css"></head><body>
<header class="top"><a href="index.php" class="brand">🗳️ SIMULASI PILKADES DIGITAL</a><nav><a href="scan.php">Pindai QR</a><a href="hasil.php">Hasil</a><a href="admin.php">Buat QR</a></nav></header>
<main class="container">
HTML;
}
function footer_html(): void { echo '</main><footer>Hanya untuk edukasi dan simulasi — bukan aplikasi pemungutan suara resmi.</footer></body></html>'; }
