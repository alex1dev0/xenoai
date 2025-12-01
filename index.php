<?php require_once 'functions.php'; ?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>XenoAI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo get_asset_url('assets/style.css'); ?>">
    <link rel="icon" href="<?php echo get_asset_url('assets/logo.png'); ?>" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="background-fx">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="grid-overlay"></div>
    </div>

    <div class="layout-container">
        <aside id="sidebar" class="sidebar collapsed">
            <div class="sidebar-header">
                <button id="new-chat-btn" class="sidebar-btn" title="Nuova Chat" type="button">
                    <i class="fas fa-plus"></i>
                    <span class="btn-text">Nuova Chat</span>
                </button>
            </div>
            <div class="chat-list-container">
                <ul id="chat-list" class="chat-list">
                </ul>
            </div>
            <div class="sidebar-footer">
                <button id="toggle-sidebar" class="sidebar-btn icon-only" title="Espandi menu">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </aside>

        <div class="app-wrapper">
            <header class="glass-header">
            <div class="brand">
                <div class="logo-container">
                    <img src="assets/logo.png" alt="XenoAI">
                    <div class="status-badge">
                        <span class="status-indicator ready"></span>
                    </div>
                </div>
                <div class="brand-info">
                <h1>XenoAI</h1>
                <span class="subtitle">Virtual Assistant</span>
            </div>
        </div>
        
        <div class="header-actions">
            <button id="mobile-menu-btn" class="mobile-menu-btn" aria-label="Menu">
                <i class="fas fa-bars"></i>
            </button>

            <button id="reset-button" class="action-btn" title="Elimina conversazione" style="display: none;">
                <i class="fas fa-trash-can"></i>
            </button>
        </div>
    </header>
        
        <main class="chat-area">
            <div class="chat-container">
                <div class="messages" id="messages">
                    <div class="message ai-message first-message">
                        <div class="message-content">
                            <p>Ciao! Come posso aiutarti oggi?</p>
                        </div>
                    </div>
                </div>
                <div class="scroll-to-bottom" id="scroll-button">
                    <i class="fas fa-arrow-down"></i>
                </div>
            </div>
        </main>

        <div id="modal-overlay" class="modal-overlay hidden">
            <div class="modal-content">
                <div class="modal-header">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Attenzione</h3>
                </div>
                <p>Sei sicuro di voler cancellare tutta la conversazione? Questa azione non può essere annullata.</p>
                <div class="modal-actions">
                    <button id="cancel-reset" class="btn-secondary">Annulla</button>
                    <button id="confirm-reset" class="btn-danger">Elimina tutto</button>
                </div>
            </div>
        </div>
        
        <div class="input-area">
            <div class="input-glass-wrapper">
                <textarea id="user-input" placeholder="Chiedimi qualcosa..." rows="1"></textarea>
                <button id="send-button" type="button">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            <footer class="mini-footer">
                <p>Powered by <span class="highlight">Ollama</span> • Created by <a href="https://alex1dev.xyz" target="_blank">Alex1Dev</a></p>
            </footer>
        </div>
    </div>
    </div>
    
    <script src="<?php echo get_asset_url('assets/script.js'); ?>"></script>
</body>
</html>