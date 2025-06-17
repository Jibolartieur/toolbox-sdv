<?php
// fichier : execute.php
// Ensure no output before headers
ob_start();

session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/php_errors.log');

// Log the start of the script
error_log("Script started");

header('Content-Type: application/json');

// Log the raw input
$raw_input = file_get_contents('php://input');
error_log("Raw input: " . $raw_input);

// Validate and sanitize input
$input = json_decode($raw_input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error: " . json_last_error_msg());
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON input: ' . json_last_error_msg()]);
    exit;
}

if (!isset($input['tool']) || !isset($input['target'])) {
    error_log("Missing parameters in input: " . print_r($input, true));
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

$tool = strtolower($input['tool']);
$raw_target = trim($input['target']);

// Normalisation spécifique selon les besoins de l'outil
switch ($tool) {
    case 'nmap':
    case 'whois':
    case 'subfinder':
    case 'sslscan':
        // Ces outils veulent juste un domaine ou une IP
        if (preg_match('/^(?:https?:\/\/)?([^\/:?#]+)(?:[\/:?#]|$)/i', $raw_target, $matches)) {
            $raw_target = $matches[1];
        }
        break;

    case 'gobuster':
    case 'nuclei':
    case 'webcheck':
    case 'whatweb':
    case 'curl':
        // Ces outils ont besoin de l'URL complète, on ne change rien
        break;

    default:
        // Par défaut, on extrait le domaine
        if (preg_match('/^(?:https?:\/\/)?([^\/:?#]+)(?:[\/:?#]|$)/i', $raw_target, $matches)) {
            $raw_target = $matches[1];
        }
        break;
}

$target = escapeshellarg($raw_target);


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
        return "nikto -h {$target}";
    },
    
        
    // Outils Web
    'gobuster' => function($target) {
    return "gobuster dir -u {$target} -w /usr/share/dirb/wordlists/common.txt -q";
    },

    'webcheck' => function($target) {
        return "curl -i 'https://web-check.xyz/api/check?url=url={$target}'";
    },
    
    // Outils d'information
    'whois' => function($target) {
        // Remove quotes added by escapeshellarg
        $target = trim($target, "'");
        
        // Check if target is an IP address
        if (filter_var($target, FILTER_VALIDATE_IP)) {
            return "whois -h whois.arin.net {$target}";
        }
        
        // Check if target is a domain
        if (preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/', $target)) {
            return "whois {$target}";
        }
        
        // If target is a URL, extract the domain
        if (preg_match('/^(?:https?:\/\/)?([^\/]+)/', $target, $matches)) {
            return "whois " . $matches[1];
        }
        
        // Default case
        return "whois {$target}";
    },
    'dig' => function($target) {
        return "dig {$target}";
    },
    
    // Outils de sécurité supplémentaires
    'sslscan' => function($target) {
        return "sslscan --no-colour {$target}";
    },


    'nuclei' => function($target) {
        return "nuclei -u {$target} --stats";
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
    error_log("Invalid tool specified: " . $tool);
    http_response_code(400);
    echo json_encode(['error' => 'Invalid tool specified']);
    exit;
}

// Build and execute command
try {
    $command = $allowed_tools[$tool]($target);
    error_log("Executing command: " . $command);
    
    // Execute command and capture output
    $output = [];
    $return_var = 0;
    
    // Check if command exists
    $command_parts = explode(' ', $command);
    $base_command = $command_parts[0];
    
    if (!`which $base_command`) {
        error_log("Command not found: " . $base_command);
        throw new Exception("Command not found: " . $base_command);
    }
    
    exec($command . " 2>&1", $output, $return_var);
    
    error_log("Command return code: " . $return_var);
    error_log("Command output: " . print_r($output, true));
    
    // Format output and ensure it's UTF-8
    $formatted_output = implode("\n", $output);
    
    // Clean any invalid UTF-8 characters
    $formatted_output = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/u', '', $formatted_output);

    
    // Save results to database
    $stmt = $pdo->prepare('INSERT INTO scan_results (user_id, tool_name, target, output) VALUES (?, ?, ?, ?)');
    $stmt->execute([$_SESSION['user_id'], $tool, trim($target, "'"), $formatted_output]);
    
    // Return results
    $response = [
        'success' => true,
        'output' => $formatted_output,
        'return_code' => $return_var
    ];
    
    // Ensure the response is valid JSON
    $json_response = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json_response === false) {
        error_log("JSON encode error: " . json_last_error_msg());
        throw new Exception('JSON encoding failed: ' . json_last_error_msg());
    }
    
    // Clear any output buffer
    ob_end_clean();
    
    echo $json_response;
    
} catch (Exception $e) {
    error_log("Exception caught: " . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'error' => 'Command execution failed',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
