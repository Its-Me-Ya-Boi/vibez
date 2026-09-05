<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vibez | Guest</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" id="computerCSS" media="screen and (min-width:900px)" href="/project/assets/dark.css">
    <link rel="stylesheet" id="mobileCSS"   media="screen and (max-width:899px)"  href="/project/assets/darkMobile.css">
    <script src="/project/settings/script.js"></script>
</head>
<body>

<?php
require('../../../vibes.php');

try {
    $result = $myPDO->query('SELECT * FROM posts ORDER BY post_id DESC');
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
    <h1 class="center">Guest mode</h1>
    <a href="/project/login.php"><h3 class="center">Login to view more</h3></a>
</header>

<div class="flexDesk" id="alignment">

    <aside class="filters">
        <h3 class="filbut center">filters</h3>
        <!-- Filters disabled for guests -->
        <button class="filbut" disabled>close friends</button><br>
        <button class="filbut" disabled>friends</button><br>
        <button class="filbut" disabled>following</button><br>
        <button class="filbut" onclick="window.location.href='/project/guest/index.php'">everyone</button><br>
    </aside>

    <main>
        <?php
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $u_id    = $row['user_id'];
            $id      = $row['post_id'];
            $puser   = htmlspecialchars($row['puser'],   ENT_QUOTES, 'UTF-8');
            $content = htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8');
            $media   = htmlspecialchars($row['media'] ?? '', ENT_QUOTES, 'UTF-8');
            $video   = htmlspecialchars($row['video'] ?? '', ENT_QUOTES, 'UTF-8');
            $time    = htmlspecialchars($row['ptime'],   ENT_QUOTES, 'UTF-8');

            echo '<div class="post">';
                echo '<div class="username"><a href="/project/guest/profiles.php?id=' . $u_id . '"><h1>' . $puser . '</h1></a></div>';
                echo '<div class="message"><p class="large">' . $content;
                if ($media !== '') echo '<br><br><img id="postImage" src="/project/assets/images/' . $media . '">';
                if ($video !== '') echo '<br><video height="150" controls><source src="/project/assets/images/' . $video . '" type="video/mp4"></video>';
                // No delete link for guests.
                echo '</p><h6 class="inline">' . $time . '</h6></div>';
                echo '<div class="comments"><a href="/project/guest/comments.php?id=' . $id . '"><p>View comments</p></a></div>';
            echo '</div>';
        }
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
                   . '<span class="inline"><a href="/project/guest/profiles.php?id=' . $user_id . '"><h3>' . $uname . '</h3></a></span>';
                if ($adminP === 'y') echo '<span class="inline"><h6>admin</h6></span>';
                echo '</div></div>';
            }
        }
        ?>
    </aside>

</div>

</body>
</html>
