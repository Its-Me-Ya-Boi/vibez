<?php
/**
 * search.php — Full-text search across post content and tags.
 * Lives at: /project/filter/search.php  (or /project/search.php)
 */

session_set_cookie_params(0);
session_start();

if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) {
    header('Location: /project/login.php');
    exit();
}

$puser   = $_SESSION['un'];
$user_id = $_SESSION['uid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vibez | search</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" id="computerCSS" media="screen and (min-width:900px)" href="/project/assets/dark.css">
    <link rel="stylesheet" id="mobileCSS"   media="screen and (max-width:899px)"  href="/project/assets/darkMobile.css">
    <script src="/project/settings/script.js"></script>
</head>
<body>

<?php
require('../../../vibes.php');
?>

<header>
    <?php include '../assets/layouts/header.php'; ?>
</header>

<main>
    <?php
    $query = $_GET['query'] ?? '';

    try {
        $stmt = $myPDO->prepare(
            'SELECT * FROM posts
              WHERE content LIKE :query OR tags LIKE :query
              ORDER BY ptime DESC'
        );
        $stmt->execute([':query' => '%' . $query . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo '<p>Search failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
        exit;
    }

    if (empty($results)) {
        echo '<p>No posts found for <strong>' . htmlspecialchars($query, ENT_QUOTES, 'UTF-8') . '</strong>.</p>';
    }

    foreach ($results as $post) {
        $pid    = $post['post_id'];
        $points = (int)($post['points'] ?? 0);

        echo '<div class="post">';
            echo '<div class="username"><a href="/project/users/profiles.php?id='
               . htmlspecialchars($post['user_id'], ENT_QUOTES, 'UTF-8') . '">'
               . '<h1>' . htmlspecialchars($post['puser'], ENT_QUOTES, 'UTF-8') . '</h1>'
               . '</a></div>';

            echo '<div class="message"><p>'
               . nl2br(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8')) . '</p>';

            if (!empty($post['media'])) {
                echo '<br><img id="postImage" src="/project/assets/images/'
                   . htmlspecialchars($post['media'], ENT_QUOTES, 'UTF-8') . '">';
            }
            if (!empty($post['video'])) {
                echo '<br><video height="150" controls><source src="/project/assets/images/'
                   . htmlspecialchars($post['video'], ENT_QUOTES, 'UTF-8') . '" type="video/mp4"></video>';
            }
            if (!empty($post['tags'])) {
                echo '<p><em>Tags: ' . htmlspecialchars($post['tags'], ENT_QUOTES, 'UTF-8') . '</em></p>';
            }

            // BUG FIX: was referencing undefined $id. Use $pid.
            echo '<button onclick="location.href=\'/project/likePost.php?id=' . $pid . '\'">♡ ' . $points . '</button><br>';

            if ($post['puser'] === $puser) {
                echo '<h6 class="inline">' . htmlspecialchars($post['ptime'], ENT_QUOTES, 'UTF-8') . '</h6>'
                   . '<a href="/project/deletePost.php?id=' . htmlspecialchars($pid, ENT_QUOTES, 'UTF-8') . '">'
                   . '<h6 class="inline" style="margin-left:75%;">delete</h6></a>';
            } else {
                echo '<h6 class="inline">' . htmlspecialchars($post['ptime'], ENT_QUOTES, 'UTF-8') . '</h6>';
            }

            echo '</div>';
            echo '<div class="comments"><a href="/project/comments.php?id='
               . htmlspecialchars($pid, ENT_QUOTES, 'UTF-8') . '"><p>View comments</p></a></div>';
        echo '</div>';
    }

    $stmt  = null;
    $myPDO = null;
    ?>
</main>

</body>
</html>
