<?php
/**
 * privacy.php — Accessible to logged-in users, admins, and guests.
 * Lives at: /project/policies/privacy.php
 */
session_start();
if (isset($_SESSION['un'], $_SESSION['uid'])) {
    $user = $_SESSION['un'];
    $uid  = $_SESSION['uid'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vibez | Privacy Policy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" id="computerCSS" media="screen and (min-width:900px)" href="/project/assets/dark.css">
    <link rel="stylesheet" id="mobileCSS"   media="screen and (max-width:899px)"  href="/project/assets/darkMobile.css">
    <script src="/project/settings/script.js"></script>
    <style>
        /*
         * BUG FIX: inline styles were hardcoded to dark-mode colours (#053C61,
         * #B68CB8, #EAE0CC). Replaced with CSS custom properties so the legal
         * pages honour the user's chosen theme.
         */
        :root {
            --legal-bg:      var(--post-bg,    #053C61);
            --legal-accent:  var(--accent,     #B68CB8);
            --legal-text:    var(--body-color,  #EAE0CC);
        }
        .legal-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 40px;
            background-color: var(--legal-bg);
            border-radius: 10px;
            line-height: 1.8;
        }
        .legal-container h1 {
            color: var(--legal-accent);
            border-bottom: 1px solid var(--legal-accent);
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .legal-container h2 {
            color: var(--legal-accent);
            margin-top: 30px;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .legal-container p,
        .legal-container ul {
            color: var(--legal-text);
            font-size: 0.95rem;
        }
        .legal-container ul { padding-left: 20px; }
        .legal-container ul li { margin-bottom: 8px; }
        .last-updated {
            font-size: 0.8rem;
            color: var(--legal-accent);
            margin-bottom: 30px;
        }
        .back-link {
            display: inline-block;
            margin: 20px 40px;
            color: var(--legal-accent);
            text-decoration: none;
        }
        .back-link:hover { text-decoration: underline; }
        
    </style>
</head>
<body>

<header>
    <?php
    if (isset($uid) && isset($_SESSION['admin']) && $_SESSION['admin'] === 'admin') {
        include $_SERVER['DOCUMENT_ROOT'] . '/project/admin/header.php';
    } elseif (isset($uid)) {
        include $_SERVER['DOCUMENT_ROOT'] . '/project/assets/layouts/header.php';
    } else { ?>
        <nav>
            <a href="/project/index.html" class="nav-logo">
                <img id="logo" src="/project/assets/images/vibezNight.png" style="width:100px;height:auto;">
            </a>
        </nav>
    <?php } ?>
</header>

<a class="back-link" href="javascript:history.back()">← Back</a>

<div class="legal-container">
        <h1>Privacy Policy</h1>
        <p class="last-updated">Last updated: April 2026</p>
        <p>Your privacy matters to us. This Privacy Policy explains what information Vibez collects, how it is used, and how it is protected. By using Vibez, you agree to the collection and use of information as described here.</p>
        <h2>Information We Collect</h2>
        <ul>
            <li><strong>Account Information:</strong> Your username, email address, and hashed password when you register.</li>
            <li><strong>Profile Information:</strong> Profile pictures, background images, and display color preferences you choose to set.</li>
            <li><strong>Content You Post:</strong> Posts, comments, images, and videos you upload to the platform.</li>
            <li><strong>Social Connections:</strong> Who you follow and who follows you back.</li>
            <li><strong>Usage Logs:</strong> IP addresses and timestamps are logged for security purposes.</li>
        </ul>
        <h2>How We Use Your Information</h2>
        <ul>
            <li>To provide and operate the Vibez platform</li>
            <li>To display your profile and content to other users</li>
            <li>To manage your account settings and preferences</li>
            <li>To protect the platform from abuse (banning, access control)</li>
            <li>To improve the site based on usage patterns</li>
        </ul>
        <h2>Information Sharing</h2>
        <p>We do not sell, rent, or share your personal information with third parties for marketing purposes. Your content (posts, profile) is visible to other registered Vibez users according to your privacy settings. Administrators may access account data when necessary to enforce platform rules.</p>
        <h2>Data Storage &amp; Security</h2>
        <p>Your data is stored in a secured database. Passwords are hashed using PHP's <code>password_hash()</code> function and are never stored in plain text. While we take reasonable precautions, no system is completely secure.</p>
        <h2>Your Rights</h2>
        <ul>
            <li>You may update your username, email, and password at any time from Settings.</li>
            <li>You may delete your account from the Settings page, which will remove your account data.</li>
            <li>You may delete your own posts and comments at any time.</li>
        </ul>
        <h2>Children's Privacy</h2>
        <p>Vibez is not intended for users under the age of 13. We do not knowingly collect information from children under 13. If you believe a minor has created an account, please contact us.</p>
        <h2>Changes to This Policy</h2>
        <p>We may update this Privacy Policy periodically. Updates will be reflected on this page with a revised date.</p>
        <h2>Contact</h2>
        <p>If you have questions or concerns about your privacy, please contact us through the Vibez platform.</p>
    </div>

</body>
</html>
