// Made by alex1dev - https://alex1dev.xyz
// file to create a database to save chats

<?php

class Database {
    private $pdo;
    
    public function __construct() {
        $db_file = __DIR__ . '/../chat_history.db';
        $db_exists = file_exists($db_file);
        
        try {
            $this->pdo = new PDO('sqlite:' . $db_file);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->exec("PRAGMA foreign_keys = ON");
            
            $this->initTables();
            
            $this->cleanupOldConversations();
        } catch (PDOException $e) {
            die("DB Connection Error: " . $e->getMessage());
        }
    }
    
    private function initTables() {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS conversations (
            id TEXT PRIMARY KEY,
            title TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            conversation_id TEXT,
            role TEXT,
            content TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(conversation_id) REFERENCES conversations(id) ON DELETE CASCADE
        )");
    }
    
    public function getPDO() {
        return $this->pdo;
    }

    public function createConversation($title = 'Nuova Chat') {
        $id = uniqid('chat_', true);
        $stmt = $this->pdo->prepare("INSERT INTO conversations (id, title) VALUES (:id, :title)");
        $stmt->execute([':id' => $id, ':title' => $title]);
        return $id;
    }

    public function getConversations() {
        $stmt = $this->pdo->query("SELECT * FROM conversations ORDER BY updated_at DESC");
        return $stmt->fetchAll();
    }

    public function getConversation($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM conversations WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function deleteConversation($id) {
        $stmt = $this->pdo->prepare("DELETE FROM conversations WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getMessages($conversation_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM messages WHERE conversation_id = :conversation_id ORDER BY created_at ASC");
        $stmt->execute([':conversation_id' => $conversation_id]);
        return $stmt->fetchAll();
    }

    public function addMessage($conversation_id, $role, $content) {
        $stmt = $this->pdo->prepare("INSERT INTO messages (conversation_id, role, content) VALUES (:conversation_id, :role, :content)");
        $stmt->execute([
            ':conversation_id' => $conversation_id,
            ':role' => $role,
            ':content' => $content
        ]);
        
        $stmt = $this->pdo->prepare("UPDATE conversations SET updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        $stmt->execute([':id' => $conversation_id]);
        
        return $this->pdo->lastInsertId();
    }

    public function updateTitle($conversation_id, $title) {
        $stmt = $this->pdo->prepare("UPDATE conversations SET title = :title WHERE id = :id");
        return $stmt->execute([':id' => $conversation_id, ':title' => $title]);
    }

    private function cleanupOldConversations() {
        $stmt = $this->pdo->prepare("DELETE FROM conversations WHERE updated_at < datetime('now', '-30 days')");
        $stmt->execute();
    }
}
?>