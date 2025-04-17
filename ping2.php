<?php
// Vérifier si l'IP est bien fournie
if (!isset($_GET['ip']) || empty($_GET['ip'])) {
    echo "Erreur : IP non spécifiée.";
    exit;
}

$ip = $_GET['ip'];

// Vérifier si l'IP est valide (IPv4 ou IPv6)
if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    echo "Erreur : Adresse IP invalide.";
    exit;
}

// Déterminer le système d'exploitation
$os = strtoupper(substr(PHP_OS, 0, 3)); // WIN pour Windows, sinon Linux/Mac

// Construire la commande adaptée
if ($os === 'WIN') {
    $cmd = "ping -n 4 " . escapeshellarg($ip); // Windows
} else {
    $cmd = "ping -c 4 " . escapeshellarg($ip); // Linux/Mac
}

// Exécuter la commande
$output = shell_exec($cmd);

if ($output === null) {
    echo "Erreur lors de l'exécution du ping.";
} else {
    echo "<pre>" . htmlspecialchars($output) . "</pre>"; // Sécuriser l'affichage
}
?>
