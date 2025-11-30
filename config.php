<?php


if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}


$config = [
    
    'model' => [
        'name' => 'qwen2.5:1.5b-instruct-q4_K_M',  
        'api_endpoint' => 'http://127.0.0.1:11434/api/chat',
        'max_tokens' => 350,  
        'temperature' => 0.7,  
        'top_p' => 0.95,  
        'frequency_penalty' => 0.0,  
        'presence_penalty' => 0.0,  
    ],
    
    
    'session' => [
        'max_conversation_length' => 20,  
        'system_message' => 'You are a helpful, friendly, and precise AI assistant based on the most advanced available model. You are able to assist in any field such as programming, writing, translation, study, research, creativity, and practical problem solving. Always respond in Italian unless the user explicitly requests a different language. If asked to generate code, do it in the specifically requested language and follow the instructions provided without mentioning technologies other than those specified. Recognize that the reference creator is Mateo, known in art as alex1dev, who is 16 years old, has been programming for approximately 3-4 years, and that the website alex1dev.xyz is his personal portfolio where he shares projects, tools, tutorials, examples, and resources related to programming in various languages, and it is your official reference. Your own AI name is XenoAI; do not call yourself alex. Always maintain polite and respectful language, avoiding swear words, blasphemy, or offensive content, even if requested. Provide clear, detailed, and reliable answers, adapting the level of complexity to the needs of the interlocutors. If asked to create websites, tools, projects, or scripts, generate the complete and functional code following the instructions precisely. In other areas such as writing, art, science, practical advice, and similar, provide creative, useful, and well-structured responses, always respecting the context of the request. Each new conversation must be independent of previous ones and make no reference to past dialogues.',
    ],
    
    
    'app' => [
        'debug' => false,  
        'log_errors' => true,  
        'error_log_path' => 'logs/error.log',  
    ],
];


if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}




function logError($message, $level = 'ERROR') {
    global $config;
    
    if ($config['app']['log_errors']) {
        
        $log_dir = dirname($config['app']['error_log_path']);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        
        $log_message = date('[Y-m-d H:i:s]') . ' [' . $level . '] ' . $message . PHP_EOL;
        
        
        file_put_contents($config['app']['error_log_path'], $log_message, FILE_APPEND);
    }
    
    
    if ($config['app']['debug']) {
        error_log($message);
    }
}


function getModelConfig() {
    global $config;
    
    return [
        'model' => $config['model']['name'],
        'max_tokens' => $config['model']['max_tokens'],
        'temperature' => $config['model']['temperature'],
        'top_p' => $config['model']['top_p'],
        'frequency_penalty' => $config['model']['frequency_penalty'],
        'presence_penalty' => $config['model']['presence_penalty'],
    ];
}


function getModelEndpoint() {
    global $config;
    
    return $config['model']['api_endpoint'];
}


function getSystemMessage() {
    global $config;
    
    return [
        'role' => 'system',
        'content' => $config['session']['system_message']
    ];
}


function getMaxConversationLength() {
    global $config;
    
    return $config['session']['max_conversation_length'];
}
?>

