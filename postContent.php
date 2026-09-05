<?php
/**
 * postContent.php — Handles text-only post form submission.
 *
 * Expects: POST['content'] (string), POST['tags'] (string, optional).
 * Lives at: /project/posts/postContent.php
 */

session_set_cookie_params(0);
session_start();

// ── Auth guard ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) {
    header('Location: /project/login.php');
    exit();
}

$puser   = $_SESSION['un'];
$user_id = (int)$_SESSION['uid'];

// ── Bootstrap ────────────────────────────────────────────────────────────────
require('../../../vibes.php');

// ── Input validation ──────────────────────────────────────────────────────────
$content = trim($_POST['content'] ?? '');
if (empty($content)) {
    die('Post content cannot be empty. <a href="/project/posts/post.php">Go back</a>');
}

$tags  = trim($_POST['tags'] ?? '');
$ptime = date('Y-m-d H:i:s');

// ── Insert ────────────────────────────────────────────────────────────────────
try {
    $stmt = $myPDO->prepare('
        INSERT INTO posts (user_id, puser, content, ptime, tags)
        VALUES (:user_id, :puser, :content, :ptime, :tags)
    ');
    $stmt->execute([
        ':user_id' => $user_id,
        ':puser'   => $puser,
        ':content' => $content,
        ':ptime'   => $ptime,
        ':tags'    => $tags,
    ]);
} catch (PDOException $e) {
    die('Could not save post: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

$stmt  = null;
$myPDO = null;

header('Location: /project/main.php');
exit();
