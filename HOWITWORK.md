# 🔒 Explication du Code de la Toolbox de Sécurité


## 🔍 Aperçu

La Toolbox de Sécurité est un ensemble complet d'outils d'analyse de sécurité conçu pour vous assister dans la réalisation de divers audits de sécurité. Elle inclut des fonctionnalités telles que le ping, traceroute, l'analyse de ports avec Nmap, les scans web avec Nikto, l'énumération de répertoires avec Dirb, ainsi que des scans spécifiques en utilisant Whois, Dig, SSLScan, Nuclei, Subfinder et WhatWeb.


## 🔐 Vérification de la session utilisateur

Dès le début, le script vérifie que l’utilisateur est bien connecté à sa session. Sinon, il renvoie une erreur 401 :

```php
session_start();
require_once 'config.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}
```

Cela évite que n’importe qui puisse exécuter des commandes sur le serveur sans être authentifié.


## 🛡 Sécurisation des entrées

Le script reçoit les données JSON envoyées par l’interface (outil + cible) et les vérifie avant de les utiliser.

```php
$raw_input = file_get_contents('php://input');
$input = json_decode($raw_input, true);

if (!isset($input['tool']) || !isset($input['target'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

$tool = strtolower($input['tool']);
$target = escapeshellarg($input['target']);
```

Utiliser escapeshellarg() permet de se protéger des injections de commandes dans les arguments passés au terminal.


## ✅ Liste blanche des outils autorisés

Pour éviter tout abus, seule une liste précise d’outils peut être utilisée. Chaque outil correspond à une fonction qui génère sa commande.

```php
$allowed_tools = [
    // Outils réseau de base
    'ping' => function($target) {
        return "ping -c 4 {$target}";
    },
    'traceroute' => function($target) {
        return "traceroute {$target}";
    },

    // Scanners réseau
    'nmap' => function($target) {
        return "nmap -sV -p- -T4 --min-rate 1000 {$target}";
    },
    'nikto' => function($target) {
        return "nikto -h {$target} -Format txt";
    },

    // Fuzzing de répertoires
    'gobuster' => function($target) {
        return "gobuster dir -u {$target} -w /usr/share/dirb/wordlists/common.txt -q";
    },

    // Requête vers une API externe
    'webcheck' => function($target) {
        return "curl -s 'https://web-check.xyz/api/check?url={$target}'";
    },

    // Informations sur l'infrastructure
    'whois' => function($target) {
        $target = trim($target, "'");
        if (filter_var($target, FILTER_VALIDATE_IP)) {
            return "whois -h whois.arin.net {$target}";
        }
        if (preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/', $target)) {
            return "whois {$target}";
        }
        if (preg_match('/^(?:https?:\/\/)?([^\/]+)/', $target, $matches)) {
            return "whois " . $matches[1];
        }
        return "whois {$target}";
    },

    'dig' => function($target) {
        return "dig +nocmd {$target} ANY +noall +answer";
    },

    // Outils de sécurité
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

Cela permet de contrôler précisément ce qui peut être exécuté et comment.


## ⚙️ Construction et exécution de la commande

Une fois la commande générée, le script vérifie si elle existe, l’exécute et récupère le résultat en nettoyant les caractères indésirables :

```php
$command = $allowed_tools[$tool]($target);
exec($command . " 2>&1", $output, $return_var);
$formatted_output = implode("\n", $output);
$formatted_output = preg_replace('/[\x00-\x1F\x7F]/u', '', $formatted_output);
```

Tous les caractères invalides sont supprimés pour éviter des problèmes d’encodage côté interface ou lors de l’enregistrement.


## 🧾 Sauvegarde des résultats en base de données

Chaque résultat est stocké en base dans la table scan_results avec l’ID utilisateur, l’outil utilisé, la cible et le résultat brut.

```php
$stmt = $pdo->prepare('INSERT INTO scan_results (user_id, tool_name, target, output) VALUES (?, ?, ?, ?)');
$stmt->execute([$_SESSION['user_id'], $tool, trim($target, "'"), $formatted_output]);
```

Cela permet de conserver l’historique des scans et de générer des rapports par la suite.


## 📤 Réponse JSON vers le frontend

Le script envoie ensuite une réponse JSON formatée, contenant le résultat brut du scan :

```php
$response = [
    'success' => true,
    'output' => $formatted_output,
    'return_code' => $return_var
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
```


## 🧯 Gestion des erreurs

Tout au long du script, les erreurs sont loggées dans un fichier (php_errors.log) pour faciliter le débogage.

```php
error_log("Exception caught: " . $e->getMessage());
http_response_code(500);
echo json_encode([
    'error' => 'Command execution failed',
    'message' => $e->getMessage()
]);
```

Cela garantit que même en cas de problème, l’application ne plante pas et reste sécurisée.


## 🔁 Conclusion

Le script execute.php est la pièce centrale de la toolbox. Il permet d’exécuter les outils de cybersécurité de manière sécurisée, automatisée et fiable tout en assurant une traçabilité des actions via la base de données et les logs. Grâce à sa structure modulaire, il est aussi facile d’y ajouter de nouveaux outils à l’avenir.
