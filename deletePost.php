<?php
/**
 * deletePost.php — Shows a post with a "confirm delete" link.
 *                  Actual deletion is handled by deleteP.php.
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vibez</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" id="computerCSS" media="screen and (min-width:900px)" href="/project/assets/dark.css">
    <link rel="stylesheet" id="mobileCSS"   media="screen and (max-width:899px)"  href="/project/assets/darkMobile.css">
    <script src="/project/settings/script.js"></script>
</head>
<body>

<?php
// ── Bootstrap ────────────────────────────────────────────────────────────────
$directory = 'assets/images/';
require('../../vibes.php');
include 'assets/layouts/header.php';

// Validate the post ID.
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die('Invalid post ID.');
}

$id = (int)$_GET['id'];

// Fetch the post to display and verify ownership.
try {
    $stmt = $myPDO->prepare('SELECT * FROM posts WHERE post_id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Could not load post: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

if (!$row) {
    die('Post not found.');
}

// Only the post owner may delete.
if ($row['user_id'] != $uid) {
    die('You are not authorized to delete this post.');
}

$pid     = $row['post_id'];
$puser   = htmlspecialchars($row['puser'],   ENT_QUOTES, 'UTF-8');
$content = htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8');
$media   = htmlspecialchars($row['media']  ?? '', ENT_QUOTES, 'UTF-8');
$video   = htmlspecialchars($row['video']  ?? '', ENT_QUOTES, 'UTF-8');
$time    = htmlspecialchars($row['ptime'],   ENT_QUOTES, 'UTF-8');

// Display the post for review before confirming.
echo '<div class="post">'
   . '<div class="username"><h1>' . $puser . '</h1></div>'
   . '<div class="message"><p class="large">';

echo $content;

if ($media !== '') {
    echo '<br><br><img id="postImage" src="/project/assets/images/' . $media . '">';
}

if ($video !== '') {
    echo '<br><br><video height="150" controls>'
       . '<source src="/project/assets/images/' . $video . '" type="video/mp4">'
       . '</video>';
}

echo '</p>'
   . '<h6 class="inline">' . $time . '</h6>'
   . '</div></div>';

echo '<a href="/project/deleteP.php?id=' . $pid . '">confirm delete</a>';
?>

</body>
</html>
