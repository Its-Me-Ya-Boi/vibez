<?php
/**
 * top.php — Top posts feed: shows the highest-rated posts to logged-in users.
 *
 * Lives at: /project/filter/top.php
 */

session_start();

// ── Auth guard ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) {
    header('Location: /project/login.php');
    exit();
}

$user = $_SESSION['un'];
$uid  = $_SESSION['uid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vibez</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" id="computerCSS" media="screen and (min-width:900px)" href="/project/assets/dark.css">
    <link rel="stylesheet" id="mobileCSS"   media="screen and (max-width:899px)"  href="/project/assets/darkMobile.css">
    <script src="/project/settings/script.js"></script>
</head>
<body>

<?php
$directory = 'assets/images/';
require('../../../vibes.php');

try {
    $result = $myPDO->query('SELECT * FROM posts ORDER BY points DESC');
} catch (PDOException $e) {
    echo '<p>Could not load posts: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
}

try {
    $stmt = $myPDO->query('SELECT * FROM users ORDER BY user_id');
} catch (PDOException $e) {
    echo '<p>Could not load users: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
}
?>

<header>
    <?php include '../assets/layouts/header.php'; ?>
</header>

<div class="flexDesk" id="alignment">

    <!-- ── Sidebar: feed filters ─────────────────────────────────────────── -->
    <aside class="filters">
        <h3 class="filbut center">filters</h3>
        <div class="filbutcont">
        <button class="filbut" onclick="window.location.href='/project/filter/close.php'">close friends</button><br>
        <button class="filbut" onclick="window.location.href='/project/filter/friends.php'">friends</button><br>
        <button class="filbut" onclick="window.location.href='/project/filter/following.php'">following</button><br>
        <button class="filbut" onclick="window.location.href='/project/main.php'">everyone</button><br>
        <button class="filbut" onclick="window.location.href='/project/filter/top.php'">top</button><br>
</div>
</aside>
    <!-- ── Main feed ─────────────────────────────────────────────────────── -->
    <main>
        <?php
        foreach ($result as $row) {
            $u_id    = $row['user_id'];
            $id      = $row['post_id'];
            $puser   = htmlspecialchars($row['puser'],   ENT_QUOTES, 'UTF-8');
            $content = htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8');
            $media   = htmlspecialchars($row['media'] ?? '', ENT_QUOTES, 'UTF-8');
            $video   = htmlspecialchars($row['video'] ?? '', ENT_QUOTES, 'UTF-8');
            $time    = htmlspecialchars($row['ptime'],   ENT_QUOTES, 'UTF-8');
            $points  = (int)($row['points'] ?? 0);

            echo '<div class="post">';

                // BUG FIX: was linking to profiles.php?id=$uid (logged-in user)
                //          instead of $u_id (post author). Fixed.
                echo '<div class="username">'
                   . '<a href="/project/users/profiles.php?id=' . $u_id . '">'
                   . '<h1>' . $puser . '</h1>'
                   . '</a></div>';

                echo '<div class="message"><p class="large">' . $content;

                if ($media !== '') {
                    echo '<br><br><img id="postImage" src="/project/assets/images/' . $media . '">';
                }
                if ($video !== '') {
                    echo '<br><video height="150" controls>'
                       . '<source src="/project/assets/images/' . $video . '" type="video/mp4">'
                       . '</video>';
                }

                echo '</p>';
                echo '<button onclick="location.href=\'/project/likePost.php?id=' . $id . '\'">♡ ' . $points . '</button><br>';

                if ($user === $puser) {
                    echo '<h6 class="inline">' . $time . '</h6>'
                       . '<a href="/project/deletePost.php?id=' . $id . '">'
                       . '<h6 class="inline" style="margin-left:75%;">delete</h6></a>';
                } else {
                    echo '<h6 class="inline">' . $time . '</h6>';
                }

                echo '</div>'; // .message
                echo '<div class="comments">'
                   . '<a href="/project/comments.php?id=' . $id . '"><p>View comments</p></a>'
                   . '</div>';

            echo '</div>'; // .post
        }
        ?>
    </main>

    <!-- ── Sidebar: user list ─────────────────────────────────────────────── -->
    <aside>
        <?php
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user_id = $row['user_id'];
            $uname   = htmlspecialchars($row['userName'], ENT_QUOTES, 'UTF-8');
            $banned  = $row['banned'];
            $propic  = htmlspecialchars($row['pic'],      ENT_QUOTES, 'UTF-8');
            $isAdmin = $row['admin'];

            if ($banned === 'n') {
                echo '<div class="user"><div class="inline">'
                   . '<img src="/project/assets/images/' . $propic . '" style="width:50px;height:50px;overflow:hidden;">'
                   . '<span class="inline"><a href="/project/users/profiles.php?id=' . $user_id . '">'
                   . '<h3>' . $uname . '</h3></a></span>';
                if ($isAdmin === 'y') echo '<span class="inline"><h6>admin</h6></span>';
                echo '</div></div>';
            }
        }
        ?>
    </aside>

</div><!-- .flexDesk -->

</body>
</html>
