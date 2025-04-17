<?php
// nmap.php
if (isset($_GET['ip']) && isset($_GET['ports'])) {
    $ip = escapeshellarg($_GET['ip']);
    $ports = escapeshellarg($_GET['ports']);

    // Vérification de la validité de l'IP
    if (!filter_var($_GET['ip'], FILTER_VALIDATE_IP)) {
        echo "Erreur : Adresse IP invalide.";
        exit;
    }

    // Construire la commande Nmap
    $command = "nmap -p $ports $ip";

    // Afficher la commande pour débogage
    echo "Commande Nmap générée : <strong>" . htmlspecialchars($command) . "</strong><br>";
    // Exécution de la commande Nmap
    exec($command, $output, $return_var);  // Capture les erreurs (stderr) aussi

    // Vérifier si la commande a échoué
    if ($return_var !== 0) {
        echo "Erreur lors de l'exécution de Nmap (code de retour : $return_var).<br>";
        echo "Sortie d'erreur :<br>";
        echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    } else {
        echo "Résultats du scan :<br>";
        echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    }
} else {
    echo "Paramètres manquants.";
}
?>
