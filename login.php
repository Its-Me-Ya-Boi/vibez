<?php
/**
 * login.php — Handles user authentication.
 *
 * On GET:  renders the login form.
 * On POST: validates credentials, sets session, redirects.
 */

session_start();
require('../vibes.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['username']) || empty($_POST['password'])) {
        $message = 'All fields are required.';
    } else {

        // Select by username only — password is verified with password_verify().
        $stmt = $myPDO->prepare('SELECT * FROM users WHERE userName = :un');
        $stmt->execute([':un' => $_POST['username']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($_POST['password'], $row['passWord'])) {

            $_SESSION['uid'] = $row['user_id'];
            $_SESSION['un']  = $row['userName'];

            $admin  = $row['admin'];
            $banned = $row['banned'];

            // Log the attempt.
            $logFile = fopen('admin/authorized.txt', 'a');
            if ($logFile) {
                fwrite($logFile, $_SERVER['REMOTE_ADDR'] . ' ' . date('F j, Y, g:i a') . "\n");
                fclose($logFile);
            }

            if ($banned === 'y') {
                // Overwrite the authorized log entry with a banned flag.
                $logFile = fopen('admin/unauthorized.txt', 'a');
                if ($logFile) {
                    fwrite($logFile, $_SERVER['REMOTE_ADDR'] . ' ' . date('F j, Y, g:i a') . " BANNED\n");
                    fclose($logFile);
                }
                header('Location: /project/banned.php');
                exit();
            }

            if ($admin === 'y') {
                $_SESSION['admin'] = 'admin';
                header('Location: /project/admin/index.php');
                exit();
            }

            header('Location: /project/main.php');
            exit();

        } else {

            // Failed attempt — log it.
            $logFile = fopen('admin/unauthorized.txt', 'a');
            if ($logFile) {
                fwrite($logFile, $_SERVER['REMOTE_ADDR'] . ' ' . date('F j, Y, g:i a') . "\n");
                fclose($logFile);
            }

            $message = 'Incorrect username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vibez | Log in</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <!-- BUG FIX: was loading only dark.css with a bare href — no theme-switching
         support and no mobile stylesheet. Matched to the rest of the project. -->
    <link rel="stylesheet" id="computerCSS" media="screen and (min-width:900px)" href="/project/assets/dark.css">
    <link rel="stylesheet" id="mobileCSS"   media="screen and (max-width:899px)"  href="/project/assets/darkMobile.css">
    <script src="/project/settings/script.js"></script>
</head>
<body>
<br>
<div class="container" style="width:500px;">

    <?php if ($message): ?>
        <p class="text-danger"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <h3>Login</h3><br>

    <form method="post">
        <label>Username</label>
        <input type="text" name="username" required>
        <br>
        <label>Password</label>
        <input type="password" name="password" required>
        <br>
        <input type="submit" name="login" value="Login">
    </form>

    <br>
    <p><a href="/project/users/newUser.php">New User</a></p>
    <p><a href="/project/guest/index.php">Continue as Guest</a></p>

</div>
<br>
</body>
</html>
