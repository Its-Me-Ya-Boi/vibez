<?php
/**
 * likeComment.php — Handle like actions for comments.
 */

session_start();

// ── Auth guard ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) {
    header('Location: /project/login.php');
    exit();
}

$user = $_SESSION['un'];   // logged-in username
$uid  = $_SESSION['uid'];  // logged-in user ID

if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $cid = (int)$_GET['id'];
} else {
    die('Invalid comment ID.');
}

REQUIRE('../../vibes.php');


try {
    $stmt = $myPDO->prepare('SELECT * FROM comments WHERE id = :id');
    $stmt->bindParam(':id', $cid, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Could not load comment: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

$count = htmlspecialchars($post['points'] ?? '0', ENT_QUOTES, 'UTF-8');

try{
    $stmt = $myPDO->prepare('SELECT * FROM commentlikes WHERE uid = :uid AND cid = :cid');
    $stmt->execute([
        ':uid' => $uid,
        ':cid' => $cid,
    ]);
    $like = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Could not check like status: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
if ($like) {
    try {
        $stmt = $myPDO->prepare('DELETE FROM commentlikes WHERE uid = :uid AND cid = :cid');
        $stmt->execute([
            ':uid' => $uid,
            ':cid' => $cid,

        ]);
    } catch (PDOException $e) {
        die('Could not remove like: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
    try {
        $stmt = $myPDO->prepare('UPDATE comments SET points = :points WHERE id = :id');
        $stmt->execute([
            ':points' => max(0, (int)$count - 1), // Prevent negative likes
            ':id'     => $cid,
        ]);
    } catch (PDOException $e) {
        die('Could not update like count: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
}else{
$count = (int)$count + 1;
try{
    $stmt = $myPDO->prepare(
        'INSERT INTO commentlikes (uid, cid) VALUES (:uid, :cid)'
    );
    $stmt->execute([
        ':uid' => $uid,
        ':cid' => $cid,
    ]);
} catch (PDOException $e) {
    die('Could not record like: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

try {
    $stmt = $myPDO->prepare('UPDATE comments SET points = :points WHERE id = :id');
    $stmt->execute([
        ':points' => $count,
        ':id'     => $cid,
    ]);
} catch (PDOException $e) {
    die('Could not update like count: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
}
header('Location: /project/comments.php/?id=' . $post['pid']);
exit();
?>