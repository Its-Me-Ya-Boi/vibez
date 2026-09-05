<<?php
/**
 * reportButton.php — Report button for posts.
 */

session_start();

// ── Auth guard ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) {
    header('Location: /project/login.php');
    exit();
}

$user = $_SESSION['un'];   // logged-in username
$uid  = $_SESSION['uid'];  // logged-in user ID
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

// ── Queries ───────────────────────────────────────────────────────────────────
try {
    $result = $myPDO->query('SELECT * FROM posts WHERE post_id = ' . intval($_GET['postid']));
} catch (PDOException $e) {
    echo '<p>Could not load posts: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
}

try {
    $stmt = $myPDO->query('SELECT * FROM users ORDER BY user_id');
} catch (PDOException $e) {
    echo '<p>Could not load users: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
}
?>

<header>
    <?php include 'assets/layouts/header.php'; ?>
</header>

<main>
    <h2 class="center">Report Post</h2>
    <h6> Please know that false reports may lead to account suspension. </h6>
    <form action="/project/report.php" method="POST" class="reportForm">
        <input type="hidden" name="postid" value="<?php echo intval($_GET['postid']); ?>">
        <label for="type">Reason for report:</label><br>
        <select name="type" id="type" required>
            <option value="">Select a reason</option>
            <option value="spam">Spam</option>
            <option value="harassment">Harassment</option>
            <option value="inappropriate">Inappropriate content</option>
            <option value="child_safety">Child safety concern</option>
            <option value="impersonation">Impersonation</option>
            <option value="other">Other</option>
        </select><br><br>
        <label for="details">Additional details (optional):</label><br>
        <textarea name="details" id="details" rows="4" cols="50" placeholder="Provide more information about the issue..."></textarea><br><br>
        <button type="submit">Submit Report</button>
    </form>
</main>