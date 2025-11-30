// Made by alex1dev - https://alex1dev.xyz
// file for proxying requests to different handlers based on 'type' parameter

<?php
header('Content-Type: application/json');

$request_type = isset($_GET['type']) ? $_GET['type'] : '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo non consentito']);
    exit;
}

switch ($request_type) {
    case 'chat':
        
        require_once 'api.php';
        break;
        
    case 'reset':
        
        require_once 'reset.php';
        break;
        
    case 'manager':
        require_once 'chat_manager.php';
        break;
        
    default:
        
        http_response_code(400);
        echo json_encode(['error' => 'Tipo di richiesta non valido']);
        exit;
}
?>

