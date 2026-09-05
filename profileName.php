<?php
/** profileName.php — Updates username across users, posts, and comments. /project/settings/ */
session_start();
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) { header('Location: /project/login.php'); exit(); }
$uid = (int)$_SESSION['uid'];
require('../../vibes.php');
$name = trim($_POST['name'] ?? '');
if (empty($name)) {
    die('Username cannot be empty. <a href="/project/settings/set.php">Back</a>');
}
try {
    $myPDO->prepare('UPDATE users    SET userName = :name WHERE user_id = :uid')->execute([':name' => $name, ':uid' => $uid]);
    $myPDO->prepare('UPDATE posts    SET puser    = :name WHERE user_id = :uid')->execute([':name' => $name, ':uid' => $uid]);
    $myPDO->prepare('UPDATE comments SET username = :name WHERE uid     = :uid')->execute([':name' => $name, ':uid' => $uid]);
    $_SESSION['un'] = $name;
} catch (PDOException $e) {
    die('Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
header('Location: /project/settings/set.php');
exit();
