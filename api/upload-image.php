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

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['image'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$file    = $_FILES['image'];
$maxSize = 5 * 1024 * 1024; // 5 MB

if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Upload error: ' . $file['error']]);
    exit;
}

if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'File too large (max 5MB)']);
    exit;
}

$finfo        = new finfo(FILEINFO_MIME_TYPE);
$mimeType     = $finfo->file($file['tmp_name']);
$allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

if (!array_key_exists($mimeType, $allowedMimes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid file type (jpg/png/webp only)']);
    exit;
}

$ext      = $allowedMimes[$mimeType];
$filename = bin2hex(random_bytes(16)) . '.' . $ext;
$destDir  = __DIR__ . '/../uploads/';
$destPath = $destDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save file']);
    exit;
}

echo json_encode(['success' => true, 'path' => 'uploads/' . $filename]);
