# 🔒 Explication du Code de la Toolbox de Sécurité

## 🔍 Aperçu

La Toolbox de Sécurité est un ensemble complet d'outils d'analyse de sécurité conçu pour vous assister dans la réalisation de divers audits de sécurité. Elle inclut des fonctionnalités telles que le ping, traceroute, l'analyse de ports avec Nmap, les scans web avec Nikto, l'énumération de répertoires avec Dirb, ainsi que des scans spécifiques en utilisant Whois, Dig, SSLScan, Nuclei, Subfinder et WhatWeb.

## 💻 Détails du Code

**Configuration Initiale**
```php
<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
```
Cette section configure l'en-tête de la réponse en JSON et active les rapports d'erreurs pour faciliter le débogage.

**Traitement des Entrées**
```php
// Validate and sanitize input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['tool']) || !isset($input['target'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

$tool = strtolower($input['tool']);
$target = escapeshellarg($input['target']);
```
Cette partie récupère et valide les données d'entrée. Elle vérifie que les paramètres requis (tool et target) sont présents et sécurise la cible contre les injections de commandes en utilisant escapeshellarg().

**Définition des Outils de Base**
```php
// Whitelist of allowed tools and their commands
$allowed_tools = [
    // Outils de base
    'ping' => function($target) {
        return "ping -c 4 {$target}";
    },
    'traceroute' => function($target) {
        return "traceroute {$target}";
    },
```
Cette section définit les outils de base pour les tests réseau. Le ping permet de vérifier si une cible est accessible, tandis que traceroute trace le chemin des paquets vers cette cible.

**Outils d'Énumération**
```php
    // Outils d'énumération
    'nmap' => function($target) {
        return "nmap -sV -p- -T4 --min-rate 1000 {$target}";
    },
    'nikto' => function($target) {
        return "nikto -h {$target} -Format txt";
    },
```
Ces fonctions permettent l'utilisation de Nmap pour la découverte de ports et services (-sV pour la détection de version, -p- pour scanner tous les ports) et Nikto pour la détection de vulnérabilités sur des serveurs web.

**Outils Web**
```php
    // Outils Web
    'dirb' => function($target) {
        return "dirb {$target} /usr/share/dirb/wordlists/common.txt -w";
    },
    'webcheck' => function($target) {
        return "curl -s 'https://web-check.xyz/api/check?url={$target}'";
    },
```
Ces outils permettent l'énumération de répertoires web avec Dirb (utilisant le dictionnaire common.txt) et la vérification générale de sites web via l'API web-check.xyz.

**Outils d'Information**
```php
    // Outils d'information
    'whois' => function($target) {
        return "whois {$target}";
    },
    'dig' => function($target) {
        return "dig +nocmd {$target} ANY +noall +answer";
    },
```
Ces fonctions permettent d'obtenir des informations sur les domaines (propriétaire, contacts, etc.) et leurs enregistrements DNS (tous types d'enregistrements).

**Outils de Sécurité Supplémentaires**
```php
    // Outils de sécurité supplémentaires
    'sslscan' => function($target) {
        return "sslscan --no-colour {$target}";
    },
    'nuclei' => function($target) {
        return "nuclei -u {$target} -silent";
    },
    'subfinder' => function($target) {
        return "subfinder -d {$target} -silent";
    },
    'whatweb' => function($target) {
        return "whatweb -a 3 {$target} --no-errors";
    }
];
```
Ces outils offrent des scans de sécurité plus avancés comme l'analyse des configurations SSL, la détection de vulnérabilités avec Nuclei (en mode silencieux), la découverte de sous-domaines et l'identification des technologies web utilisées (avec un niveau d'agressivité 3).

**Validation de l'Outil Demandé**
```php
// Validate tool
if (!array_key_exists($tool, $allowed_tools)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid tool specified']);
    exit;
}
```
Cette fonction vérifie si l'outil demandé est présent dans la liste blanche des outils autorisés. Si ce n'est pas le cas, elle renvoie une erreur 400.

**Exécution de la Commande et Récupération des Résultats**
```php
// Build and execute command
try {
    $command = $allowed_tools[$tool]($target);
    
    // Execute command and capture output
    $output = [];
    $return_var = 0;
    exec($command . " 2>&1", $output, $return_var);
    
    // Format output
    $formatted_output = implode("\n", $output);
    
    // Return results
    echo json_encode([
        'success' => true,
        'output' => $formatted_output,
        'return_code' => $return_var
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Command execution failed',
        'message' => $e->getMessage()
    ]);
}
```
Cette section construit la commande en utilisant la fonction associée à l'outil sélectionné, l'exécute via exec() et capture sa sortie. Elle redirige également les erreurs (2>&1) vers la sortie standard pour les capturer. Les résultats sont ensuite formatés en JSON avec un indicateur de succès, la sortie de la commande et son code de retour. En cas d'erreur, elle renvoie un message d'erreur formaté.
