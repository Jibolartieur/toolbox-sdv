<?php
// fichier run_gobuster
if (!isset($_GET['target'])) {
    echo json_encode(['success' => false, 'error' => 'No target']);
    exit;
}

$target = escapeshellarg($_GET['target']);
$wordlist = '/usr/share/dirb/wordlists/common.txt';

$cmd = "gobuster dir -u $target -w $wordlist -k 2>&1";
exec($cmd, $output, $status);

if ($status !== 0) {
    echo json_encode(['success' => false, 'error' => 'Gobuster failed', 'raw' => implode("\n", $output)]);
    exit;
}

// Garde uniquement les lignes utiles
$found = array_filter($output, fn($line) => str_contains($line, "Status:"));

echo json_encode(['success' => true, 'results' => array_values($found)]);

