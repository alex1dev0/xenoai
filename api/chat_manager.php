<?php

// Made by alex1dev - https://alex1dev.xyz
// This file is needed to save conversations and manage them

header('Content-Type: application/json');
require_once 'db.php';

$db = new Database();

$action = isset($_GET['action']) ? $_GET['action'] : '';
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($action) {
        case 'list':
            $user_id = isset($input['user_id']) ? $input['user_id'] : null;
            $conversations = $db->getConversations($user_id);
            echo json_encode(['conversations' => $conversations]);
            break;

        case 'create':
            $title = isset($input['title']) ? $input['title'] : 'Nuova Chat';
            $user_id = isset($input['user_id']) ? $input['user_id'] : null;
            $id = $db->createConversation($title, $user_id);
            echo json_encode(['id' => $id, 'title' => $title]);
            break;

        case 'delete':
            if (!isset($input['id'])) {
                throw new Exception('ID mancante');
            }
            $db->deleteConversation($input['id']);
            echo json_encode(['success' => true]);
            break;

        case 'get':
            if (!isset($_GET['id'])) {
                throw new Exception('ID mancante');
            }
            $messages = $db->getMessages($_GET['id']);
            echo json_encode(['messages' => $messages]);
            break;
            
        case 'rename':
            if (!isset($input['id']) || !isset($input['title'])) {
                throw new Exception('Dati mancanti');
            }
            $db->updateTitle($input['id'], $input['title']);
            echo json_encode(['success' => true]);
            break;

        default:
            throw new Exception('Azione non valida');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
