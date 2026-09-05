<?php
/** profilePass.php — Updates the user's password. /project/settings/ */
session_start();
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) { header('Location: /project/login.php'); exit(); }
$uid = (int)$_SESSION['uid'];
require('../../vibes.php');
$pass = $_POST['pass'] ?? '';
if (empty($pass)) {
    die('Password cannot be empty. <a href="/project/settings/set.php">Back</a>');
}
$hashed = password_hash($pass, PASSWORD_DEFAULT);
try {
    $stmt = $myPDO->prepare('UPDATE users SET passWord = :pass WHERE user_id = :uid');
    $stmt->execute([':pass' => $hashed, ':uid' => $uid]);
} catch (PDOException $e) {
    die('Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
header('Location: /project/settings/set.php');
exit();
