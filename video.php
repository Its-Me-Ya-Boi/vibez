<?php
/**
 * video.php — Form for creating a post with a video attachment.
 *
 * Lives at: /project/posting/video.php
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
    <form action="/project/posting/postVideo.php" method="post"
          enctype="multipart/form-data" id="postType">

        <textarea id="content" name="content" placeholder="type something..."
                  oninput="autoExpand(this)"></textarea>

        <!-- Accept only MP4; the server also validates via finfo. -->
        <input type="file" name="media" id="media" accept="video/mp4" required><br>

        <textarea id="tags" name="tags" placeholder="add some tags..."
                  oninput="autoExpand(this)"></textarea>

        <input type="submit" value="Upload" name="submit" id="submit">
    </form>
</main>

</body>
</html>
