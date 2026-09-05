<?php
/**
 * close.php — Feed filtered to close friends only.
 * Lives at: /project/filter/close.php
 */

session_start();

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
?>

<header>
    <?php include '../assets/layouts/header.php'; ?>
</header>

<div class="flexDesk" id="alignment">

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

    <main>
        <?php
        $result   = $myPDO->query('SELECT * FROM posts ORDER BY post_id DESC');
        $allPosts = $result->fetchAll(PDO::FETCH_ASSOC);

        $stmt3 = $myPDO->prepare('SELECT following_id FROM following WHERE user_id = ? AND close_friend = 1');
        $stmt3->execute([$uid]);
        $closeFriendIds = $stmt3->fetchAll(PDO::FETCH_COLUMN);

        if (empty($closeFriendIds)) {
            echo "<p>You have no close friends yet. Visit someone's profile and add them as a close friend!</p>";
        } else {
            foreach ($allPosts as $row) {
                $u_id    = $row['user_id'];
                $id      = $row['post_id'];
                $puser   = htmlspecialchars($row['puser'],   ENT_QUOTES, 'UTF-8');
                $content = htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8');
                $media   = htmlspecialchars($row['media'] ?? '', ENT_QUOTES, 'UTF-8');
                $video   = htmlspecialchars($row['video'] ?? '', ENT_QUOTES, 'UTF-8');
                $time    = htmlspecialchars($row['ptime'],   ENT_QUOTES, 'UTF-8');
                // BUG FIX: was referencing undefined $post['points']. Use $row.
                $points  = (int)($row['points'] ?? 0);

                if (!in_array($u_id, $closeFriendIds)) continue;

                echo '<div class="post">';
                    echo '<div class="username"><a href="/project/users/profiles.php?id=' . $u_id . '"><h1>' . $puser . '</h1></a></div>';
                    echo '<div class="message"><p class="large">' . $content;
                    if ($media !== '') echo '<br><br><img id="postImage" src="/project/assets/images/' . $media . '">';
                    if ($video !== '') echo '<br><video height="150" controls><source src="/project/assets/images/' . $video . '" type="video/mp4"></video>';
                    echo '</p>';
                    echo '<button onclick="location.href=\'/project/likePost.php?id=' . $id . '\'">♡ ' . $points . '</button><br>';
                    if ($user === $puser) {
                        echo '<h6 class="inline">' . $time . '</h6><a href="/project/deletePost.php?id=' . $id . '"><h6 class="inline" style="margin-left:75%;">delete</h6></a>';
                    } else {
                        echo '<h6 class="inline">' . $time . '</h6>';
                    }
                    echo '</div>';
                    echo '<div class="comments"><a href="/project/comments.php?id=' . $id . '"><p>View comments</p></a></div>';
                echo '</div>';
            }
        }

        $stmt = $myPDO->query('SELECT * FROM users ORDER BY user_id');
        ?>
    </main>

    <aside>
        <?php
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user_id = $row['user_id'];
            $uname   = htmlspecialchars($row['userName'], ENT_QUOTES, 'UTF-8');
            $adminP  = $row['admin'];
            $banned  = $row['banned'];
            $propic  = htmlspecialchars($row['pic'],      ENT_QUOTES, 'UTF-8');
            if ($banned === 'n') {
                echo '<div class="user"><div class="inline"><img src="/project/assets/images/' . $propic . '" style="width:50px;height:50px;overflow:hidden;">'
                   . '<span class="inline"><a href="/project/users/profiles.php?id=' . $user_id . '"><h3>' . $uname . '</h3></a></span>';
                if ($adminP === 'y') echo '<span class="inline"><h6>admin</h6></span>';
                echo '</div></div>';
            }
        }
        ?>
    </aside>

</div>

</body>
</html>
