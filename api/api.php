<?php

// Made by alex1dev - https://alex1dev.xyz
// File for handling AI model API requests with streaming responses

if (function_exists('apache_setenv')) {
    @apache_setenv('no-gzip', 1);
}
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
ob_implicit_flush(1);

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');

ini_set('display_errors', 0);

require_once '../config.php';
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo non consentito']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['message']) || empty(trim($input['message']))) {
    http_response_code(400);
    echo json_encode(['error' => 'Messaggio mancante']);
    exit;
}

$db = new Database();
$conversation_id = isset($input['conversation_id']) ? $input['conversation_id'] : null;
$is_new_conversation = false;

try {
    if (!$conversation_id) {
        $title = substr(strip_tags($input['message']), 0, 30) . '...';
        $user_id = isset($input['user_id']) ? $input['user_id'] : null;
        $conversation_id = $db->createConversation($title, $user_id);
        $is_new_conversation = true;
    } else {
        $chat = $db->getConversation($conversation_id);
        if (!$chat) {
            $title = substr(strip_tags($input['message']), 0, 30) . '...';
            $user_id = isset($input['user_id']) ? $input['user_id'] : null;
            $conversation_id = $db->createConversation($title, $user_id);
            $is_new_conversation = true;
        }
    }

    echo "data: " . json_encode([
        'type' => 'meta',
        'conversation_id' => $conversation_id,
        'new_chat' => $is_new_conversation
    ]) . "\n\n";
    flush();

    if (!$db->addMessage($conversation_id, 'user', $input['message'])) {
         throw new Exception('Failed to save user message');
    }

    $db_messages = $db->getMessages($conversation_id);
    $context_messages = [];
    
    $context_messages[] = getSystemMessage();
    
    foreach ($db_messages as $msg) {
        $context_messages[] = [
            'role' => $msg['role'],
            'content' => $msg['content']
        ];
    }

    $max_length = getMaxConversationLength();
    if (count($context_messages) > $max_length) {
        $system_msg = $context_messages[0];
        $history = array_slice($context_messages, 1);
        $history = array_slice($history, -($max_length - 1));
        $context_messages = array_merge([$system_msg], $history);
    }

    $model_config = getModelConfig();
    $model_config['messages'] = $context_messages;
    
    streamLocalAIModel($model_config, getModelEndpoint(), $db, $conversation_id);

} catch (Exception $e) {
    echo "data: " . json_encode([
        'type' => 'error',
        'error' => 'Errore durante la comunicazione con il modello AI',
        'details' => $e->getMessage()
    ]) . "\n\n";
    logError('Errore API AI: ' . $e->getMessage());
}

function streamLocalAIModel($data, $endpoint, $db, $conversation_id) {
    $ollama_data = [
        'model' => $data['model'],
        'messages' => $data['messages'],
        'stream' => true, 
        'options' => [
            'temperature' => (float)$data['temperature'],
            'top_p' => (float)$data['top_p'],
            'frequency_penalty' => (float)$data['frequency_penalty'],
            'presence_penalty' => (float)$data['presence_penalty']
        ]
    ];
    
    $ch = curl_init($endpoint);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($ollama_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120); 
    
    $full_response = "";

    curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $chunk) use (&$full_response) {
        $lines = explode("\n", $chunk);
        foreach ($lines as $line) {
            if (trim($line) === '') continue;
            
            $data = json_decode($line, true);
            if ($data) {
                $content = '';
                if (isset($data['message']['content'])) {
                    $content = $data['message']['content'];
                } elseif (isset($data['response'])) {
                    $content = $data['response'];
                }

                if ($content !== '') {
                    $full_response .= $content;
                    echo "data: " . json_encode([
                        'type' => 'chunk',
                        'content' => $content
                    ]) . "\n\n";
                    flush();
                }
                
                if (isset($data['done']) && $data['done']) {
                    return strlen($chunk);
                }
            }
        }
        return strlen($chunk);
    });
    
    curl_exec($ch);
    
    if (curl_errno($ch)) {
        throw new Exception('Errore cURL: ' . curl_error($ch));
    }
    
    curl_close($ch);

    $db->addMessage($conversation_id, 'assistant', $full_response);
    
    echo "data: " . json_encode(['type' => 'done']) . "\n\n";
    flush();
}
?>