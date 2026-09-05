<?php
/** profileLInk.php — Updates link colour. /project/settings/ */
session_start();
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) { header('Location: /project/login.php'); exit(); }
$uid = (int)$_SESSION['uid'];
require('../../vibes.php');
try {
    $stmt = $myPDO->prepare('UPDATE profiles SET lcolor = :link WHERE usr_id = :uid');
    $stmt->execute([':link' => $_POST['link'], ':uid' => $uid]);
} catch (PDOException $e) {
    die('Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
header('Location: /project/settings/set.php');
exit();
