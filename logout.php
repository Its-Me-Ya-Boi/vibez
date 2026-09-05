<?php
/**
 * logout.php — Destroys the current session and redirects to login.
 */

session_start();
session_destroy();

header('Location: /project/login.php');
exit();
