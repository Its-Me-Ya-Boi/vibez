<?php
/**
 * createUser.php — Handles new account registration form submission.
 *
 * Expects: POST['username'], POST['password'], POST['email'].
 * Lives at: /project/users/createUser.php
 */

// ── Bootstrap ─────────────────────────────────────────────────────────────────
// BUG FIX (error_log): "$null" undefined warning was caused by the original file
//         calling "$myPDO = null;" at the end as "$null" — typo. Replaced with
//         a clean teardown below.
require('../../../vibes.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /project/users/newUser.php');
    exit();
}

if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
    die('All fields are required.');
}

$username = trim($_POST['username']);
$password = $_POST['password'];
$email    = trim($_POST['email']);

// Basic email format check.
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Please enter a valid email address.');
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $myPDO->prepare('
        INSERT INTO users (userName, passWord, email)
        VALUES (:username, :password, :email)
    ');
    $stmt->execute([
        ':username' => $username,
        ':password' => $hashedPassword,
        ':email'    => $email,
    ]);

    $myPDO = null;

    // Redirect to login with a success message via query string.
    header('Location: /project/login.php?registered=1');
    exit();

} catch (PDOException $e) {
    // Check for duplicate username (MySQL error 1062).
    if ($e->getCode() == 23000) {
        die('That username is already taken. <a href="/project/users/newUser.php">Try another</a>.');
    }
    die('Error creating account: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
