<?php
/**
 * follow.php — Toggles the follow relationship between the logged-in user
 *              and the profile they are viewing.
 *
 * Expects: GET['id'] — the target user's ID (integer).
 * Lives at: /project/users/follow.php
 */

session_start();

// ── Auth guard ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) {
    header('Location: /project/login.php');
    exit();
}

$uid  = (int)$_SESSION['uid'];
$user = $_SESSION['un'];

// Validate the target ID.
if (!isset($_GET['id']) || !ctype_digit($_GET['id']) || (int)$_GET['id'] === 0) {
    header('Location: /project/main.php');
    exit();
}

$fid = (int)$_GET['id'];

// Prevent self-following.
if ($fid === $uid) {
    header('Location: /project/users/profile.php');
    exit();
}

// ── Bootstrap ────────────────────────────────────────────────────────────────
require('../../../vibes.php');

try {
    // Check whether the follow relationship already exists.
    $stmt = $myPDO->prepare('
        SELECT 1 FROM following WHERE user_id = :uid AND following_id = :fid
    ');
    $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
    $stmt->bindParam(':fid', $fid, PDO::PARAM_INT);
    $stmt->execute();
    $isFollowing = (bool)$stmt->fetch();

    if ($isFollowing) {
        // Already following — unfollow.
        $stmtUnfollow = $myPDO->prepare('
            DELETE FROM following WHERE user_id = :uid AND following_id = :fid
        ');
        $stmtUnfollow->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmtUnfollow->bindParam(':fid', $fid, PDO::PARAM_INT);
        $stmtUnfollow->execute();
    } else {
        // Not following — follow.
        $stmtFollow = $myPDO->prepare('
            INSERT INTO following (user_id, following_id) VALUES (:uid, :fid)
        ');
        $stmtFollow->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmtFollow->bindParam(':fid', $fid, PDO::PARAM_INT);
        $stmtFollow->execute();
    }

    header('Location: /project/users/profiles.php?id=' . $fid);
    exit();

} catch (PDOException $e) {
    die('Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
