<?php
/**
 * set.php — User settings page.
 *           Handles theme switching and links to individual profile-update scripts.
 *
 * Lives at: /project/settings/set.php
 */

session_start();

// ── Auth guard ────────────────────────────────────────────────────────────────
// BUG FIX: original had no exit() after the redirect, and used a relative path.
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
    <title>Vibez | Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" id="computerCSS" media="screen and (min-width:900px)" href="/project/assets/dark.css">
    <link rel="stylesheet" id="mobileCSS"   media="screen and (max-width:899px)"  href="/project/assets/darkMobile.css">
    <script src="/project/settings/script.js" defer></script>
</head>
<body>

<header>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/project/assets/layouts/header.php'; ?>
</header>

<div class="settings-container">

    <!-- ── Theme ─────────────────────────────────────────────────────────── -->
    <section class="setting-group">
        <h2>Theme</h2>
        <div class="dropdown">
            <button class="dropbtn">Theme</button>
            <div class="dropdown-content">
                <a href="#" onclick="setTheme('light')">Light Mode</a>
                <a href="#" onclick="setTheme('dark')">Dark Mode</a>
                <a href="#" onclick="setTheme('black')">Black Mode</a>
            </div>
        </div>
    </section>

    <!-- ── Account ───────────────────────────────────────────────────────── -->
    <section class="setting-group">
        <h2>Account Settings</h2>

        <form class="setting-form" method="post" action="/project/settings/profileName.php">
            <label class="setting-label">Change username:</label>
            <input class="setting-input" type="text" name="name" required>
            <button class="setting-submit" type="submit">Save</button>
        </form>

        <form class="setting-form" method="post" action="/project/settings/profilePass.php">
            <label class="setting-label">Change password:</label>
            <input class="setting-input" type="password" name="pass" placeholder="New password" required>
            <button class="setting-submit" type="submit">Save</button>
        </form>

        <form class="setting-form" method="post" action="/project/settings/profileEmail.php">
            <label class="setting-label">Change email:</label>
            <input class="setting-input" type="email" name="email" placeholder="New email" required>
            <button class="setting-submit" type="submit">Save</button>
        </form>

        <form class="setting-form" method="post" action="/project/settings/profilePicture.php" enctype="multipart/form-data">
            <label class="setting-label">Profile picture:</label>
            <input class="setting-input" type="file" name="picture">
            <button class="setting-submit" type="submit" name="submit">Upload</button>
        </form>
    </section>

    <!-- ── Appearance ────────────────────────────────────────────────────── -->
    <section class="setting-group">
        <h2>Profile Appearance</h2>

        <form class="setting-form" method="post" action="/project/settings/profileBackground.php" enctype="multipart/form-data">
            <label class="setting-label">Background image:</label>
            <input class="setting-input" type="file" name="background">
            <label class="setting-label">Background colour:</label>
            <input class="setting-input" type="color" name="color">
            <button class="setting-submit" type="submit">Save</button>
        </form>

        <form class="setting-form" method="post" action="/project/settings/profileButton.php">
            <label class="setting-label">Button colour:</label>
            <input class="setting-input" type="color" name="button">
            <button class="setting-submit" type="submit">Save</button>
        </form>

        <form class="setting-form" method="post" action="/project/settings/profileText.php">
            <label class="setting-label">Text colour:</label>
            <input class="setting-input" type="color" name="text">
            <button class="setting-submit" type="submit">Save</button>
        </form>

        <form class="setting-form" method="post" action="/project/settings/profileUserColor.php">
            <label class="setting-label">Username colour:</label>
            <input class="setting-input" type="color" name="username">
            <button class="setting-submit" type="submit">Save</button>
        </form>

        <form class="setting-form" method="post" action="/project/settings/profilePost.php">
            <label class="setting-label">Post colour:</label>
            <input class="setting-input" type="color" name="post">
            <button class="setting-submit" type="submit">Save</button>
        </form>

        <form class="setting-form" method="post" action="/project/settings/profileComment.php">
            <label class="setting-label">Comment colour:</label>
            <input class="setting-input" type="color" name="comment">
            <button class="setting-submit" type="submit">Save</button>
        </form>

        <form class="setting-form" method="post" action="/project/settings/profileLInk.php">
            <label class="setting-label">Link colour:</label>
            <input class="setting-input" type="color" name="link">
            <button class="setting-submit" type="submit">Save</button>
        </form>
    </section>

    <!-- ── Danger zone ───────────────────────────────────────────────────── -->
    <section class="setting-group danger-zone">
        <h2>Danger Zone</h2>
        <form class="setting-form" method="post" action="/project/settings/delete.php">
            <label class="setting-label">Delete account:</label>
            <button class="setting-submit delete-btn" type="submit">Delete</button>
        </form>
    </section>

</div>

</body>
</html>
