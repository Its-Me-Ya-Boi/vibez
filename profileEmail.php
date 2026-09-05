<?php
/** profileEmail.php — Updates the user's email address. /project/settings/ */
session_start();
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) { header('Location: /project/login.php'); exit(); }
$user = $_SESSION['un'];
$uid  = (int)$_SESSION['uid'];
require('../../vibes.php');
$email = trim($_POST['email'] ?? '');
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Please provide a valid email address. <a href="/project/settings/set.php">Back</a>');
}
try {
    $stmt = $myPDO->prepare('UPDATE users SET email = :email WHERE user_id = :uid');
    $stmt->execute([':email' => $email, ':uid' => $uid]);
} catch (PDOException $e) {
    die('Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
header('Location: /project/settings/set.php');
exit();
