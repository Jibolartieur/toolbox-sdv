<?php
// config.php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // À modifier selon votre configuration
define('DB_PASS', 'P@ssw0rd92');         // À modifier selon votre configuration
define('DB_NAME', 'security_toolbox');

// Configuration de la session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // À mettre à 0 si vous n'utilisez pas HTTPS

// Connexion à la base de données
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fonction pour rediriger si non connecté
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
} 
