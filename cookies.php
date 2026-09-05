<?php
/**
 * cookies.php — Accessible to logged-in users, admins, and guests.
 * Lives at: /project/policies/cookies.php
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
    <title>Vibez | Cookies Policy</title>
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
        <h1>Cookies Policy</h1>
        <p class="last-updated">Last updated: April 2026</p>
        <p>This Cookies Policy explains how Vibez uses cookies and similar technologies when you visit or use our platform. By continuing to use Vibez, you agree to our use of cookies as described below.</p>
        <h2>What Are Cookies?</h2>
        <p>Cookies are small text files stored on your device when you visit a website. They help websites remember information about your visit, making your experience more consistent and useful.</p>
        <h2>How We Use Cookies</h2>
        <ul>
            <li><strong>Session Cookies:</strong> We use PHP session cookies to keep you logged in while you navigate the site. These are deleted when you close your browser or log out.</li>
            <li><strong>Preference Cookies:</strong> We store your light/dark mode preference in <code>localStorage</code> and <code>sessionStorage</code> so your display settings are remembered between pages.</li>
            <li><strong>Security Cookies:</strong> Session data is used to verify your identity and protect your account from unauthorized access.</li>
        </ul>
        <h2>We Do Not Use</h2>
        <ul>
            <li>Third-party advertising cookies</li>
            <li>Tracking or analytics cookies from external services</li>
            <li>Cookies that share your data with other websites</li>
        </ul>
        <h2>Managing Cookies</h2>
        <p>You can control and delete cookies through your browser settings. Please note that disabling session cookies will prevent you from logging in to Vibez. Clearing localStorage will reset your theme preference to the default dark mode.</p>
        <h2>Changes to This Policy</h2>
        <p>We may update this Cookies Policy from time to time. Any changes will be posted on this page with an updated date.</p>
        <h2>Contact</h2>
        <p>If you have any questions about our use of cookies, please reach out through the Vibez platform.</p>
    </div>

</body>
</html>
