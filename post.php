<?php
/**
 * post.php — Form for creating a text-only post.
 *
 * Lives at: /project/posting/post.php
 */

session_set_cookie_params(0);
session_start();

// ── Auth guard ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) {
    header('Location: /project/login.php');
    exit();
}

$un  = $_SESSION['un'];
$uid = $_SESSION['uid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vibez | Post</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" id="computerCSS" media="screen and (min-width:900px)" href="/project/assets/dark.css">
    <link rel="stylesheet" id="mobileCSS"   media="screen and (max-width:899px)"  href="/project/assets/darkMobile.css">
    <script src="/project/settings/script.js"></script>
    <style>
        /* Post form layout — supplements the global stylesheet. */
        #photoType {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
    </style>
    <script>
        function autoExpand(el) {
            el.style.height = 'auto';
            el.style.height = el.scrollHeight + 'px';
        }
    </script>
</head>
<body>

<header><?php include '../assets/layouts/header.php'; ?></header>

<main>
    <form action="/project/posting/postContent.php" method="post" id="postType">
        <textarea id="content" name="content" placeholder="type something..."
                  oninput="autoExpand(this)"></textarea>
        <textarea id="tags"    name="tags"    placeholder="add some tags..."
                  oninput="autoExpand(this)"></textarea>
        <input type="submit" value="Post" name="submit" id="submit">
    </form>
</main>

</body>
</html>
