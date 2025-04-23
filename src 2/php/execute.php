<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Validate and sanitize input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['tool']) || !isset($input['target'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

$tool = strtolower($input['tool']);
$target = escapeshellarg($input['target']);

// Whitelist of allowed tools and their commands
$allowed_tools = [
    // Outils de base
    'ping' => function($target) {
        return "ping -c 4 {$target}";
    },
    'traceroute' => function($target) {
        return "traceroute {$target}";
    },
    
    // Outils d'énumération
    'nmap' => function($target) {
        return "nmap -sV -p- -T4 --min-rate 1000 {$target}";
    },
    'nikto' => function($target) {
        return "nikto -h {$target} -Format txt";
    },
    
    // Outils Web
    'dirb' => function($target) {
        return "dirb {$target} /usr/share/dirb/wordlists/common.txt -w";
    },
    'webcheck' => function($target) {
        return "curl -s 'https://web-check.xyz/api/check?url={$target}'";
    },
    
    // Outils d'information
    'whois' => function($target) {
        return "whois {$target}";
    },
    'dig' => function($target) {
        return "dig +nocmd {$target} ANY +noall +answer";
    },
    
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

// Validate tool
if (!array_key_exists($tool, $allowed_tools)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid tool specified']);
    exit;
}

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
