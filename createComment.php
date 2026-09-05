<?php
/**
 * createComment.php — Handles new comment form submission,
 *                     then redirects back to the comments page.
 *
 * Expects: POST['postid'] (int), POST['comment'] (string).
 */

session_set_cookie_params(0);
session_start();

// ── Auth guard ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) {
    header('Location: /project/login.php');
    exit();
}

$puser = $_SESSION['un'];
$uid   = $_SESSION['uid'];

// ── Bootstrap ────────────────────────────────────────────────────────────────
require('../../vibes.php');

// ── Input validation ──────────────────────────────────────────────────────────
if (empty($_POST['postid']) || !ctype_digit((string)$_POST['postid'])) {
    die('Invalid post ID.');
}

if (empty(trim($_POST['comment'] ?? ''))) {
    die('Comment cannot be empty.');
}

$pid     = (int)$_POST['postid'];
$content = trim($_POST['comment']);
$ctime   = date('Y-m-d H:i:s');

// ── Insert comment ────────────────────────────────────────────────────────────
try {
    $stmt = $myPDO->prepare(
        'INSERT INTO comments (uid, username, pid, content, ctime)
         VALUES (:uid, :username, :pid, :content, :ctime)'
    );
    $stmt->execute([
        ':uid'      => $uid,
        ':username' => $puser,
        ':pid'      => $pid,
        ':content'  => $content,
        ':ctime'    => $ctime,
    ]);
} catch (PDOException $e) {
    die('Could not save comment: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

$stmt  = null;
$myPDO = null;

// Redirect back to the post's comment page.
header('Location: /project/comments.php?id=' . $pid);
exit();
