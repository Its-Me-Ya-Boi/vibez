<?php // profileBackground.php
/**
 * Handles background image upload and/or background colour update.
 * Lives at: /project/settings/profileBackground.php
 */
session_start();
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) { header('Location: /project/login.php'); exit(); }
$uid = (int)$_SESSION['uid'];
require('../../vibes.php');

$background = $_FILES['background']['name']  ?? '';
$color      = $_POST['color']                ?? '';

if (!empty($background)) {
    $targetDir  = $_SERVER['DOCUMENT_ROOT'] . '/project/assets/backgrounds/';
    $targetFile = $targetDir . basename($background);
    if (move_uploaded_file($_FILES['background']['tmp_name'], $targetFile)) {
        $stmt = $myPDO->prepare('UPDATE profiles SET bgimg = :bg WHERE usr_id = :uid');
        $stmt->execute([':bg' => $background, ':uid' => $uid]);
    }
}

if (!empty($color)) {
    $stmt = $myPDO->prepare('UPDATE profiles SET bgcolor = :color WHERE usr_id = :uid');
    $stmt->execute([':color' => $color, ':uid' => $uid]);
}

header('Location: /project/settings/set.php');
exit();
