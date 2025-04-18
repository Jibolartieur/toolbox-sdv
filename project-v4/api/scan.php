<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $target = $data['target'] ?? '';
    $tool = $data['tool'] ?? '';

    if (!filter_var($target, FILTER_VALIDATE_IP)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid IP address']);
        exit;
    }

    try {
        $output = '';
        switch ($tool) {
            case 'ping':
                exec("ping -c 4 " . escapeshellarg($target), $output);
                $output = implode("\n", $output);
                break;

            case 'nmap':
                exec("nmap -sS -sV -p 1-1000 " . escapeshellarg($target), $output);
                $output = implode("\n", $output);
                break;

            case 'nikto':
                exec("nikto -h " . escapeshellarg($target), $output);
                $output = implode("\n", $output);
                break;

            default:
                http_response_code(400);
                echo json_encode(['error' => 'Invalid tool']);
                exit;
        }

        $conn = getConnection();
        $stmt = $conn->prepare("INSERT INTO scan_results (id, tool, target, output, user_id) VALUES (UUID(), ?, ?, ?, ?)");
        $stmt->execute([$tool, $target, $output, $_SESSION['user_id']]);

        echo json_encode([
            'tool' => $tool,
            'target' => $target,
            'output' => $output,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error']);
    }
}