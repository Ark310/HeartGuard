<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];

// Session check (GET with no logout param)
if ($method === 'GET' && !isset($_GET['logout'])) {
    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
        if ((time() - ($_SESSION['last_activity'] ?? 0)) > 7200) {
            session_destroy();
            echo json_encode(['authenticated' => false]);
        } else {
            $_SESSION['last_activity'] = time();
            echo json_encode(['authenticated' => true]);
        }
    } else {
        echo json_encode(['authenticated' => false]);
    }
    exit;
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    echo json_encode(['success' => true]);
    exit;
}

// Login
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['password']) || !is_string($input['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing password']);
        exit;
    }
    if (password_verify($input['password'], ADMIN_PASSWORD_HASH)) {
        $_SESSION['admin']         = true;
        $_SESSION['last_activity'] = time();
        echo json_encode(['success' => true]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid password']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
