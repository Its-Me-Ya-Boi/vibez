<?php
/**
 * deleteC.php — Executes comment deletion after owner confirmation.
 *               Called from the "confirm delete" link in deleteComment.php.
 *
 * Expects: GET['id'] — the comment ID (integer).
 */

session_start();

// ── Auth guard ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) {
    header('Location: /project/login.php');
    exit();
}

$user = $_SESSION['un'];
$uid  = $_SESSION['uid'];

// ── Bootstrap ────────────────────────────────────────────────────────────────
$directory = 'assets/images/';
require('../../vibes.php');

// Validate the comment ID.
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die('Invalid comment ID.');
}

$id = (int)$_GET['id'];

// Fetch the comment to verify ownership before deleting.
try {
    $stmt = $myPDO->prepare('SELECT * FROM comments WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Could not load comment: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

if (!$comment) {
    die('Comment not found.');
}

// Only the comment owner may delete.
if ($comment['uid'] != $uid) {
    die('You are not authorized to delete this comment.');
}
  //add to delete reports
        $file = $_SERVER['DOCUMENT_ROOT'] . '/project/admin/moderation/deleted.txt';
        $text = "Post ID: " . $comment['post_id'] . " | Comment ID: " . $id . " | User ID: " . $comment['uid'] . " | Username: " . $comment['username'] . " | Content: " . $comment['content'] . " | Time: " . $comment['time'] . "\n\n";
        file_put_contents($file, $text, FILE_APPEND | LOCK_EX);
// Delete the comment.
try {
    $del = $myPDO->prepare('DELETE FROM comments WHERE id = :id');
    $del->execute([':id' => $id]);
} catch (PDOException $e) {
    die('Could not delete comment: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

$del   = null;
$myPDO = null;

// BUG FIX: was using a JS redirect — replaced with a proper server-side redirect.
header('Location: /project/main.php');
exit();
