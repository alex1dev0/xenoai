<div align="center">
  <img src="https://socialify.git.ci/alex1dev0/xenoai/image?description=1&font=Inter&language=1&name=1&owner=1&pattern=Transparent&theme=Auto" alt="xenoai" width="500" />
</div>

<div align="center">

![License](https://img.shields.io/github/license/alex1dev0/xenoai?style=flat-square) ![Top Language](https://img.shields.io/github/languages/top/alex1dev0/xenoai?style=flat-square) ![Repo Size](https://img.shields.io/github/repo-size/alex1dev0/xenoai?style=flat-square) ![Issues](https://img.shields.io/github/issues/alex1dev0/xenoai?style=flat-square) ![Stars](https://img.shields.io/github/stars/alex1dev0/xenoai?style=flat-square) 

<p align="center">
  *Developed with the software and tools below.*
</p>

[![contributors](https://img.shields.io/github/contributors/alex1dev0/xenoai?style=flat-square)](https://github.com/alex1dev0/xenoai/graphs/contributors)
[![forks](https://img.shields.io/github/forks/alex1dev0/xenoai?style=flat-square)](https://github.com/alex1dev0/xenoai/network/members)
[![stars](https://img.shields.io/github/stars/alex1dev0/xenoai?style=flat-square)](https://github.com/alex1dev0/xenoai/stargazers)

</div>

---

# XenoAI - Chat Interface

A modern, responsive, and lightweight chat interface built with PHP, SQLite, and vanilla JavaScript, designed to interact with local LLMs (Large Language Models) via Ollama.

## Table of Contents

- [Languages](#languages)
- [Features](#features)
- [Getting Started](#getting-started)
- [Configuration](#configuration)
- [Project Structure](#project-structure)
- [Contributing](#contributing)
- [License](#license)

## Languages

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white) ![CSS](https://img.shields.io/badge/CSS-1572B6?style=for-the-badge&logo=css3&logoColor=white) ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=white) ![SQLite](https://img.shields.io/badge/SQLite-07405E?style=for-the-badge&logo=sqlite&logoColor=white)

## Features

- **Local AI Integration**: Seamlessly connects to Ollama (default model: `qwen2.5:1.5b-instruct-q4_K_M`) for privacy-focused AI interactions.
- **Modern UI/UX**: 
  - Glassmorphism design with dynamic background effects.
  - Fully responsive layout (mobile-first approach) with swipe gestures.
  - Smooth typing indicators and real-time streaming responses (SSE).
  - Markdown support with code highlighting.
- **Chat Management**:
  - Create multiple independent conversations.
  - Sidebar history with auto-generated titles.
  - Rename and delete chat capabilities.
- **Data Persistence**:
  - Uses SQLite for robust, server-less data storage.
  - Automatic database creation and initialization on first run.
- **Performance**:
  - No heavy frameworks (Vanilla JS, PHP).
  - Efficient asset caching.
  - Device-based session management.

## Getting Started

### Prerequisites

- **PHP 8.0+** (with `sqlite3` and `pdo_sqlite` extensions enabled).
- **Web Server** (Apache, Nginx, or PHP built-in server).
- **Ollama** running locally (default port: `11434`).

### Installation

1. Clone the repository:
```bash
git clone https://github.com/alex1dev0/xenoai.git
cd xenoai
```

2. Configure Ollama:
Ensure your Ollama instance is running and has the required model installed:
```bash
ollama pull qwen2.5:1.5b-instruct-q4_K_M
```
*Note: You can change the model in `config.php`.*

### Running the App

You can serve the project using PHP's built-in server:

```bash
php -S localhost:8000
```

Access `http://localhost:8000` in your browser.

## Configuration

You can customize the application by editing `config.php`.

### Changing the AI Model
Modify the `model` array in `config.php`:
```php
'model' => [
    'name' => 'llama3', // Change to your preferred model
    'api_endpoint' => 'http://127.0.0.1:11434/api/chat',
    'max_tokens' => 1024,
    // ... other parameters
],
```

### Modifying the System Prompt
Customize the AI's persona in `config.php` under `'session' => 'system_message'`. The default persona is a helpful assistant named XenoAI.

### Logs
Error logs are stored in `logs/error.log` by default. You can configure logging behavior in the `app` section of `config.php`.

## Project Structure

```text
/
├── api/
│   ├── api.php           # Handles AI model communication (SSE streaming)
│   ├── chat_manager.php  # CRUD operations for conversations
│   ├── db.php            # Database connection and schema management
│   ├── proxy.php         # Central routing for API requests
│   └── reset.php         # Session reset functionality
├── assets/
│   ├── script.js         # Frontend logic (DOM manipulation, Fetch API)
│   ├── style.css         # Main stylesheet (CSS Variables, Flexbox/Grid)
│   └── ...               # Images
├── logs/                 # Application logs
├── config.php            # Configuration settings
├── functions.php         # Helper functions
├── index.php             # Main application entry point
└── READMEBASE.md         # This file
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

Distributed under the MIT License. See `LICENSE` for more information.

---

Created by **Alex1Dev**
