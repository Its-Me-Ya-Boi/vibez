<?php
/**
 * deleteComment.php — Shows a comment with a "confirm delete" link.
 *                     Actual deletion is handled by deleteC.php.
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

// Validate the comment ID.
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die('Invalid comment ID.');
}

$id = (int)$_GET['id'];

// Fetch the comment to display and check ownership.
try {
    $stmt = $myPDO->prepare('SELECT * FROM comments WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Could not load comment: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

if (!$row) {
    die('Comment not found.');
}

// Only the comment owner may delete.
if ($row['uid'] != $uid) {
    die('You are not authorized to delete this comment.');
}

// Display the comment and a confirm link.
echo '<div class="comment">'
   . '<h4>'  . htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') . '</h4>'
   . '<p>'   . htmlspecialchars($row['content'],  ENT_QUOTES, 'UTF-8') . '</p>'
   . '<br><h6 class="inline">' . htmlspecialchars($row['ctime'], ENT_QUOTES, 'UTF-8') . '</h6>'
   . '</div>';

echo '<a href="/project/deleteC.php?id=' . $id . '">confirm delete</a>';
?>

</body>
</html>
