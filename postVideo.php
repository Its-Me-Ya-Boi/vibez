<?php
/**
 * postVideo.php — Handles video post form submission.
 *                 Validates file type via finfo, stores with a unique name,
 *                 then inserts the post record.
 *
 * Expects: POST['content'], POST['tags'], FILES['media'] (MP4 only).
 * Lives at: /project/posts/postVideo.php
 */

session_set_cookie_params(0);
session_start();

// ── Auth guard ────────────────────────────────────────────────────────────────
// BUG FIX: bootstrap was before the auth check — moved auth guard first.
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) {
    header('Location: /project/login.php');
    exit();
}

// ── Bootstrap ────────────────────────────────────────────────────────────────
require('../../../vibes.php');

$puser = $_SESSION['un'];
$uid   = (int)$_SESSION['uid'];
$tags  = trim($_POST['tags'] ?? '');

// ── File validation ───────────────────────────────────────────────────────────
if (!isset($_FILES['media']) || $_FILES['media']['error'] !== UPLOAD_ERR_OK) {
    die('No file uploaded or upload error.');
}

$allowed  = ['video/mp4' => 'mp4'];
$maxSize  = 100 * 1024 * 1024; // 100 MB for video

$finfo    = new finfo(FILEINFO_MIME_TYPE);
$fileMime = $finfo->file($_FILES['media']['tmp_name']);

if (!array_key_exists($fileMime, $allowed)) {
    die('Only MP4 files are allowed.');
}

if ($_FILES['media']['size'] > $maxSize) {
    die('File too large. Maximum size is 100 MB.');
}

// Generate a collision-resistant filename.
$newName    = uniqid('video_', true) . '.' . $allowed[$fileMime];
$targetDir  = $_SERVER['DOCUMENT_ROOT'] . '/project/assets/images/';
$targetFile = $targetDir . $newName;

// Move the file before the DB insert so we can roll back on failure.
if (!move_uploaded_file($_FILES['media']['tmp_name'], $targetFile)) {
    die('File move failed. Check directory permissions.');
}

// ── Insert post ───────────────────────────────────────────────────────────────
try {
    $stmt = $myPDO->prepare('
        INSERT INTO posts (puser, user_id, video, content, ptime, tags)
        VALUES (:puser, :user_id, :video, :content, NOW(), :tags)
    ');
    $stmt->execute([
        ':puser'   => $puser,
        ':user_id' => $uid,
        ':video'   => $newName,
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
