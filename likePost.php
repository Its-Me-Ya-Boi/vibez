<?php
/**
 * main.php — Main feed: shows all posts to logged-in users.
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
    $postId = (int)$_GET['id'];
} else {
    die('Invalid post ID.');
}

REQUIRE('../../vibes.php');


try {
    $stmt = $myPDO->prepare('SELECT * FROM posts WHERE post_id = :id');
    $stmt->bindParam(':id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Could not load post: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

$count = htmlspecialchars($post['points'] ?? '0', ENT_QUOTES, 'UTF-8');

try{
    $stmt = $myPDO->prepare('SELECT * FROM likes WHERE uid = :uid AND pid = :pid');
    $stmt->execute([
        ':uid' => $uid,
        ':pid' => $postId,
    ]);
    $like = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Could not check like status: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
if ($like) {
    try {
        $stmt = $myPDO->prepare('DELETE FROM likes WHERE uid = :uid AND pid = :pid');
        $stmt->execute([
            ':uid' => $uid,
            ':pid' => $postId,
        ]);
    } catch (PDOException $e) {
        die('Could not remove like: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
    try {
        $stmt = $myPDO->prepare('UPDATE posts SET points = :points WHERE post_id = :id');
        $stmt->execute([
            ':points' => max(0, (int)$count - 1), // Prevent negative likes
            ':id'     => $postId,
        ]);
    } catch (PDOException $e) {
        die('Could not update like count: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
}else{
$count = (int)$count + 1;
try{
    $stmt = $myPDO->prepare(
        'INSERT INTO likes (uid, pid) VALUES (:uid, :pid)'
    );
    $stmt->execute([
        ':uid' => $uid,
        ':pid' => $postId,
    ]);
} catch (PDOException $e) {
    die('Could not record like: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

try {
    $stmt = $myPDO->prepare('UPDATE posts SET points = :points WHERE post_id = :id');
    $stmt->execute([
        ':points' => $count,
        ':id'     => $postId,
    ]);
} catch (PDOException $e) {
    die('Could not update like count: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
}
header('Location: /project/comments.php/?id=' . $postId);
exit();
?>