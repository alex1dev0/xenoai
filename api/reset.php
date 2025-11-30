// Made by alex1dev - https://alex1dev.xyz
// file needed to delete the current conversation

<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);

session_start();

require_once '../config.php';

$_SESSION['conversation'] = [
    getSystemMessage()
];

echo json_encode(['success' => true, 'message' => 'Conversazione resettata']);
?>

