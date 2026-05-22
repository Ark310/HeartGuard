<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ((time() - ($_SESSION['last_activity'] ?? 0)) > 7200) {
    session_destroy();
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Session expired']);
    exit;
}

$_SESSION['last_activity'] = time();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if ($data === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

$contentPath = __DIR__ . '/../data/content.json';
$tmpPath     = $contentPath . '.tmp.' . getmypid();
$json        = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if (file_put_contents($tmpPath, $json) === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Write failed']);
    exit;
}

if (!rename($tmpPath, $contentPath)) {
    @unlink($tmpPath);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Save failed']);
    exit;
}

echo json_encode(['success' => true]);
