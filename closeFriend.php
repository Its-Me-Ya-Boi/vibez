<?php
/**
 * closeFriend.php — Toggles the close-friend flag on an existing follow
 *                   relationship between the logged-in user and the target.
 *
 * Expects: GET['id'] — the target user's ID (integer).
 * Lives at: /project/users/closeFriend.php
 */

session_start();

// ── Auth guard ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) {
    header('Location: /project/login.php');
    exit();
}

$user = $_SESSION['un'];
$uid  = (int)$_SESSION['uid'];

// Validate the target ID.
// BUG FIX: was using $_GET['id'] raw in bindParam after already casting to $fid.
//          Now uses $fid consistently throughout.
if (!isset($_GET['id']) || !ctype_digit($_GET['id']) || (int)$_GET['id'] === 0) {
    header('Location: /project/main.php');
    exit();
}

$fid = (int)$_GET['id'];

// ── Bootstrap ────────────────────────────────────────────────────────────────
require('../../../vibes.php');

try {
    // Check whether a follow row exists and its current close_friend value.
    $stmt = $myPDO->prepare('
        SELECT close_friend FROM following
         WHERE user_id = :uid AND following_id = :fid
    ');
    $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
    $stmt->bindParam(':fid', $fid, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        // Can't mark as close friend if not following at all.
        header('Location: /project/users/profiles.php?id=' . $fid);
        exit();
    }

    if ($row['close_friend'] == 1) {
        // Already a close friend — remove the flag.
        $stmtUpdate = $myPDO->prepare('
            UPDATE following SET close_friend = 0
             WHERE user_id = :uid AND following_id = :fid
        ');
    } else {
        // Not a close friend — add the flag.
        $stmtUpdate = $myPDO->prepare('
            UPDATE following SET close_friend = 1
             WHERE user_id = :uid AND following_id = :fid
        ');
    }

    $stmtUpdate->bindParam(':uid', $uid, PDO::PARAM_INT);
    $stmtUpdate->bindParam(':fid', $fid, PDO::PARAM_INT);
    $stmtUpdate->execute();

    header('Location: /project/users/profiles.php?id=' . $fid);
    exit();

} catch (PDOException $e) {
    die('Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
