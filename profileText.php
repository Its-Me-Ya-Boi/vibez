<?php
/** profileText.php — Updates text colour. /project/settings/ */
session_start();
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) { header('Location: /project/login.php'); exit(); }
$uid = (int)$_SESSION['uid'];
require('../../vibes.php');
try {
    $stmt = $myPDO->prepare('UPDATE profiles SET tcolor = :text WHERE usr_id = :uid');
    $stmt->execute([':text' => $_POST['text'], ':uid' => $uid]);
} catch (PDOException $e) {
    die('Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
header('Location: /project/settings/set.php');
exit();
