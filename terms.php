<?php
/**
 * terms.php — Accessible to logged-in users, admins, and guests.
 * Lives at: /project/policies/terms.php
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
    <title>Vibez | Terms of Use</title>
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
        .highlight-box {
            border-left: 3px solid var(--legal-accent);
            padding: 10px 20px;
            margin: 20px 0;
            background-color: rgba(182,140,184,0.08);
            border-radius: 0 5px 5px 0;
        }
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
        <h1>Terms of Use</h1>
        <p class="last-updated">Last updated: April 2026</p>
        <div class="highlight-box">
            <p>By creating an account or using Vibez, you agree to these Terms of Use. Please read them carefully. If you do not agree, do not use the platform.</p>
        </div>
        <h2>1. Eligibility</h2>
        <p>You must be at least 13 years old to use Vibez. By registering, you confirm that you meet this age requirement. Accounts found to belong to users under 13 may be removed without notice.</p>
        <h2>2. Your Account</h2>
        <ul>
            <li>You are responsible for keeping your password secure.</li>
            <li>You are responsible for all activity that occurs under your account.</li>
            <li>You may not share your account with others or create accounts on behalf of others without their knowledge.</li>
            <li>You may only have one active account unless explicitly permitted.</li>
        </ul>
        <h2>3. Acceptable Use</h2>
        <p>You agree not to use Vibez to:</p>
        <ul>
            <li>Post content that is illegal, threatening, harassing, defamatory, or abusive</li>
            <li>Share content that infringes on someone else's intellectual property rights</li>
            <li>Upload malicious files, spam, or automated bot activity</li>
            <li>Impersonate another person or misrepresent your identity</li>
            <li>Attempt to gain unauthorized access to any part of the platform or another user's account</li>
            <li>Post explicit or adult content without appropriate platform authorization</li>
        </ul>
        <h2>4. Content You Post</h2>
        <p>You retain ownership of the content you post on Vibez. By posting, you grant Vibez a non-exclusive license to display your content to other users as part of the platform's normal operation. You are solely responsible for the content you share.</p>
        <h2>5. Content Moderation</h2>
        <p>Vibez administrators reserve the right to remove any content that violates these Terms or that is otherwise deemed inappropriate, without notice. Repeat violations may result in account suspension or a permanent ban.</p>
        <h2>6. Account Termination</h2>
        <p>You may delete your account at any time from the Settings page. Vibez reserves the right to suspend or terminate any account that violates these Terms, with or without prior notice.</p>
        <h2>7. Disclaimer of Warranties</h2>
        <p>Vibez is provided "as is" without warranties of any kind. We do not guarantee that the platform will be available at all times, error-free, or secure. Use the platform at your own risk.</p>
        <h2>8. Limitation of Liability</h2>
        <p>To the fullest extent permitted by law, Vibez and its administrators are not liable for any indirect, incidental, or consequential damages arising from your use of the platform, including loss of data or unauthorized access to your account.</p>
        <h2>9. Changes to These Terms</h2>
        <p>We may revise these Terms of Use at any time. Continued use of Vibez after changes are posted constitutes your acceptance of the updated terms.</p>
        <h2>10. Contact</h2>
        <p>If you have any questions about these Terms, please contact us through the Vibez platform.</p>
    </div>

</body>
</html>
