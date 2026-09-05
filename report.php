<?php
/**
 * report.php — Handles new report form submission,
 *                     then redirects back to the main page.
 *
 * Expects: POST['postid'] (int), POST['comment'] (string).
 */

session_set_cookie_params(0);
session_start();

// ── Auth guard ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) {
    header('Location: /project/login.php');
    exit();
}

$puser = $_SESSION['un'];
$uid   = $_SESSION['uid'];

// ── Bootstrap ────────────────────────────────────────────────────────────────
require('../vibes.php');
// ── Input validation ──────────────────────────────────────────────────────────
if (empty($_POST['postid']) || !ctype_digit((string)$_POST['postid'])) {
    die('Invalid post ID.');
}

// ── Fetch post to get info for report ───────────────────────────────────────
$postid = intval($_POST['postid']);
$query = $myPDO->prepare("SELECT * FROM posts WHERE post_id = :postid");
$query->bindParam(':postid', $postid, PDO::PARAM_INT);
$query->execute();
$info = $query->fetch(PDO::FETCH_ASSOC);
if (!$info) {
    die('Post not found.');
}
$usid = $info['user_id'];

//importance is determined by the type of report.
$importance = 'low';
if (isset($_POST['type'])) {
    switch ($_POST['type']) {
        case 'spam':
            $importance = '10';
            break;
        case 'harassment':
            $importance = '3';
            break;
        case 'inappropriate':
            $importance = '2';
            break;
        case 'child_safety':
            $importance = '1';
            break;
        case 'impersonation':
            $importance = '5';
            break;
        case 'other':
            $importance = '8';
            break;
        default:
            $importance = '9';
    }
}

// ── Insert report into database ────────────────────────────────────────────────
$stmt = $myPDO->prepare("INSERT INTO reports (pid, uid, content, media, video, importance, time) VALUES (:postid, :uid, :content, :image, :media, :importance, :time)");
$stmt->bindParam(':postid', $postid, PDO::PARAM_INT);
$stmt->bindParam(':uid', $usid, PDO::PARAM_INT);
$stmt->bindParam(':content', $info['content'], PDO::PARAM_STR);
$stmt->bindParam(':image', $info['media'], PDO::PARAM_STR);
$stmt->bindParam(':media', $info['video'], PDO::PARAM_STR);
$stmt->bindParam(':importance', $importance, PDO::PARAM_STR);
$stmt->bindParam(':time', date('Y-m-d H:i:s'), PDO::PARAM_STR);
$stmt->execute();

// ── Redirect back to the main page ──────────────────────────────────────────────
header('Location: /project/main.php');
exit();