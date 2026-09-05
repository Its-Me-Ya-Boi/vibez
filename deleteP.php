<?php
/**
 * deleteP.php — Executes post deletion after owner confirmation.
 *               Called from the "confirm delete" link in deletePost.php.
 *
 * Expects: GET['id'] — the post ID (integer).
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

// Validate the post ID.
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die('Invalid post ID.');
}

$id = (int)$_GET['id'];

// Fetch the post to verify ownership before deleting.
try {
    $stmt = $myPDO->prepare('SELECT * FROM posts WHERE post_id = :id');
    $stmt->execute([':id' => $id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Could not load post: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

if (!$post) {
    die('Post not found.');
}

// Only the post owner may delete.
if ($post['user_id'] != $uid) {
    die('You are not authorized to delete this post.');
}
//add to delete reports
$file = $_SERVER['DOCUMENT_ROOT'] . '/project/admin/moderation/deleted.txt';
$text = "Post ID: " . $id . " | User ID: " . $post['user_id'] . " | Username: " . $post['puser'] . " | Content: " . $post['content'] . " | Time: " . $post['ptime'] . "\n\n";
file_put_contents($file, $text, FILE_APPEND | LOCK_EX);
// Delete the post.
try {
    $del = $myPDO->prepare('DELETE FROM posts WHERE post_id = :id');
    $del->execute([':id' => $id]);
} catch (PDOException $e) {
    die('Could not delete post: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

$del   = null;
$myPDO = null;

/*header('Location: /project/main.php');
exit();
*/
echo 'Post deleted successfully. <a href="/project/main.php">Return to main page.</a>';