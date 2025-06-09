<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

header('Content-Type: application/json');

try {
    // Récupérer les résultats de l'utilisateur, triés par date décroissante
    $stmt = $pdo->prepare('
        SELECT tool_name, target, output, created_at 
        FROM scan_results 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ');
    $stmt->execute([$_SESSION['user_id']]);
    $results = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'results' => $results
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur lors de la récupération des résultats',
        'message' => $e->getMessage()
    ]);
} 