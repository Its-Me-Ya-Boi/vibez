<?php
/**
 * postMedia.php — Handles image post form submission.
 *                 Validates file type via finfo, stores with a unique name,
 *                 then inserts the post record.
 *
 * Expects: POST['content'], POST['tags'], FILES['media'].
 * Lives at: /project/posts/postMedia.php
 */

session_set_cookie_params(0);
session_start();

// ── Auth guard ────────────────────────────────────────────────────────────────
// BUG FIX: bootstrap (require) was before the auth check — DB connected even
//          for unauthenticated requests. Auth guard moved first.
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) {
    header('Location: /project/login.php');
    exit();
}

// ── Bootstrap ────────────────────────────────────────────────────────────────
require('../../../vibes.php');

$tags = trim($_POST['tags'] ?? '');

// ── File validation ───────────────────────────────────────────────────────────
if (!isset($_FILES['media']) || $_FILES['media']['error'] !== UPLOAD_ERR_OK) {
    die('No file uploaded or upload error.');
}

$allowedMime = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
];

$maxSize  = 20 * 1024 * 1024; // 20 MB
$fileTmp  = $_FILES['media']['tmp_name'];
$fileSize = $_FILES['media']['size'];

// Validate by actual MIME type, not the client-supplied extension.
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$fileMime = $finfo->file($fileTmp);

if (!array_key_exists($fileMime, $allowedMime)) {
    die('Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.');
}

if ($fileSize > $maxSize) {
    die('File too large. Maximum size is 20 MB.');
}

// Generate a collision-resistant filename.
$ext        = $allowedMime[$fileMime];
$newName    = uniqid('media_', true) . '.' . $ext;
$targetDir  = $_SERVER['DOCUMENT_ROOT'] . '/project/assets/images/';
$targetFile = $targetDir . $newName;

if (!move_uploaded_file($fileTmp, $targetFile)) {
    die('Upload failed. Check directory permissions.');
}

// ── Insert post ───────────────────────────────────────────────────────────────
try {
    $stmt = $myPDO->prepare('
        INSERT INTO posts (puser, user_id, media, content, ptime, tags)
        VALUES (:puser, :user_id, :media, :content, NOW(), :tags)
    ');
    $stmt->execute([
        ':puser'   => $_SESSION['un'],
        ':user_id' => (int)$_SESSION['uid'],
        ':media'   => $newName,
        ':content' => trim($_POST['content'] ?? ''),
        ':tags'    => $tags,
    ]);
} catch (PDOException $e) {
    // Roll back the uploaded file if the DB insert fails.
    unlink($targetFile);
    die('Database error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

$stmt  = null;
$myPDO = null;

header('Location: /project/main.php');
exit();
