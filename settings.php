<?php
/**
 * settings.php — User settings page (theme switcher etc.).
 *
 * Lives at: /project/settings/settings.php
 * vibes.php is therefore at: ../../vibes.php  (NOT ../../../vibes.php)
 */

session_start();

// ── Auth guard ────────────────────────────────────────────────────────────────
// BUG FIX (error_log): auth block didn't call exit() after the redirect,
//         and the redirect pointed to ../index.php (relative — unreliable).
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
    <!-- BUG FIX (error_log): script path was '../script.js' — wrong directory.
         css.js lives alongside script.js in /project/settings/. -->
    <script src="/project/settings/script.js"></script>
</head>
<body>

<?php
// ── Bootstrap ─────────────────────────────────────────────────────────────────
// BUG FIX (error_log): path was '../../../vibes.php' which resolves to the wrong
//         level. settings.php is at /project/settings/, so vibes.php is two
//         levels up at ../../vibes.php.
$directory = '../assets/images/';
require('../../vibes.php');
?>

<header>
    <?php include '../assets/layouts/header.php'; ?>
</header>

<div class="settings-container">

    <!-- ── Theme switcher ────────────────────────────────────────────────── -->
    <div class="setting-group">
        <h2>Appearance</h2>
        <p>Choose your preferred colour theme. Your choice is saved and applied across all pages.</p>
        <br>

        <!-- Each button calls setMode() in css.js with the desktop + mobile sheet paths -->
        <button class="filbut" onclick="setMode(
            '/project/assets/dark.css',
            '/project/assets/darkMobile.css',
            '/project/assets/images/vibezNight.png'
        )">Dark (purple)</button>

        <button class="filbut" onclick="setMode(
            '/project/assets/black.css',
            '/project/assets/blackMobile.css',
            '/project/assets/images/vibezNight.png'
        )">Black</button>

        <button class="filbut" onclick="setMode(
            '/project/assets/light.css',
            '/project/assets/lightMobile.css',
            '/project/assets/images/vibezDay.png'
        )">Light (warm)</button>
    </div>

</div>

</body>
</html>
