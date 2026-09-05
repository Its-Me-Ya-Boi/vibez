<?php
/**
 * media.php — Form for creating a post with an image attachment.
 *
 * Lives at: /project/posting/media.php
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
    <form action="/project/posting/postMedia.php" method="post"
          enctype="multipart/form-data" id="postType">

        <textarea id="content" name="content" placeholder="type something..."
                  oninput="autoExpand(this)"></textarea>

        <input type="file" name="media" id="media" accept="image/*" required><br>

        <textarea id="tags" name="tags" placeholder="add some tags..."
                  oninput="autoExpand(this)"></textarea>

        <input type="submit" value="Upload" name="submit" id="submit">
    </form>

    <!-- Live image preview before upload -->
    <img id="filePreview" src="#" alt="Preview" style="display:none; width:200px; margin-top:10px;">

    <script>
        document.getElementById('media').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (ev) {
                const img = document.getElementById('filePreview');
                img.src = ev.target.result;
                img.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    </script>
</main>

</body>
</html>
