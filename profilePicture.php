<?php
/** profilePicture.php — Handles profile picture upload. /project/settings/ */
session_start();
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) { header('Location: /project/login.php'); exit(); }
$uid = (int)$_SESSION['uid'];
require('../../vibes.php');

if (!isset($_FILES['picture']) || $_FILES['picture']['error'] !== UPLOAD_ERR_OK) {
    die('No file uploaded or upload error. <a href="/project/settings/set.php">Back</a>');
}

$allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
$ext = strtolower(pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowedTypes)) {
    die('Only JPG, JPEG, PNG, and GIF files are allowed. <a href="/project/settings/set.php">Back</a>');
}

if (!getimagesize($_FILES['picture']['tmp_name'])) {
    die('Uploaded file is not a valid image. <a href="/project/settings/set.php">Back</a>');
}

$targetDir  = $_SERVER['DOCUMENT_ROOT'] . '/project/assets/images/';
$filename   = basename($_FILES['picture']['name']);
$targetFile = $targetDir . $filename;

if (file_exists($targetFile)) {
    die('A file with that name already exists. <a href="/project/settings/set.php">Back</a>');
}

if (!move_uploaded_file($_FILES['picture']['tmp_name'], $targetFile)) {
    die('Upload failed. <a href="/project/settings/set.php">Back</a>');
}

try {
    $stmt = $myPDO->prepare('UPDATE users SET pic = :pic WHERE user_id = :uid');
    $stmt->execute([':pic' => $filename, ':uid' => $uid]);
} catch (PDOException $e) {
    die('Error saving picture: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

header('Location: /project/settings/set.php');
exit();
