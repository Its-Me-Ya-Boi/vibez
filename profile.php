<?php
/**
 * profile.php — The logged-in user's own profile page.
 *               Shows their posts, comments, friends, and following list,
 *               with their custom theme applied.
 *
 * Lives at: /project/users/profile.php
 */

session_start();

// ── Auth guard ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) {
    header('Location: /project/login.php');
    exit();
}

$user = $_SESSION['un'];
$uid  = (int)$_SESSION['uid'];

// ── Bootstrap ────────────────────────────────────────────────────────────────
// BUG FIX (error_log): was '../../../../vibes.php' — too many levels up.
//         profile.php is at /project/users/, so vibes.php is ../../../vibes.php.
require('../../../vibes.php');

// ── Load own profile theme colours ───────────────────────────────────────────
try {
    $stmtbg = $myPDO->prepare('SELECT * FROM profiles WHERE usr_id = :uid');
    $stmtbg->bindParam(':uid', $uid, PDO::PARAM_INT);
    $stmtbg->execute();
    $resbg = $stmtbg->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $resbg = [];
}

if (!empty($resbg)) {
    $bgimg  = htmlspecialchars($resbg[0]['bgimg'],        ENT_QUOTES, 'UTF-8');
    $bgcolor = htmlspecialchars($resbg[0]['bgcolor'],     ENT_QUOTES, 'UTF-8');
    $bcolor  = htmlspecialchars($resbg[0]['button_color'],ENT_QUOTES, 'UTF-8');
    $pcolor  = htmlspecialchars($resbg[0]['pcolor'],      ENT_QUOTES, 'UTF-8');
    $ccolor  = htmlspecialchars($resbg[0]['ccolor'],      ENT_QUOTES, 'UTF-8');
    $ucolor  = htmlspecialchars($resbg[0]['ucolor'],      ENT_QUOTES, 'UTF-8');
    $tcolor  = htmlspecialchars($resbg[0]['tcolor'],      ENT_QUOTES, 'UTF-8');
    $lcolor  = htmlspecialchars($resbg[0]['lcolor'],      ENT_QUOTES, 'UTF-8');
} else {
    $bgimg  = '';
    $bgcolor = '#27073B';
    $bcolor  = '#053C61';
    $pcolor  = '#053C61';
    $ccolor  = '#27073B';
    $ucolor  = '#B68CB8';
    $tcolor  = '#fff';
    $lcolor  = '#B68CB8';
}
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
    <style>
        body, .background {
            background-image: url('/project/assets/backgrounds/<?= $bgimg; ?>');
            background-color: <?= $bgcolor; ?>;
            background-size: cover;
            background-attachment: fixed;
            color: <?= $tcolor; ?>;
        }
        .filbut, button       { background-color: <?= $bcolor; ?>; color: <?= $tcolor; ?>; }
        .post                 { background-color: <?= $pcolor; ?>; color: <?= $tcolor; ?>; }
        .comment              { background-color: <?= $ccolor; ?>; color: <?= $tcolor; ?>; margin: 5px; }
        .username             { color: <?= $ucolor; ?>; }
        .user                 { background-color: <?= $bgcolor; ?>; }
        .friend {
            background-color: <?= $ccolor; ?>;
            margin: 5px;
            border-radius: 5px;
            width: 75px;
            text-align: center;
            padding: 5px;
            padding-top: 10px;
        }
        a, .comments a, .friend a, .status a, .username a, .filbut a { color: <?= $lcolor; ?>; }
    </style>
</head>
<body>

<?php
$directory = '/project/assets/images/';

// ── Data queries ──────────────────────────────────────────────────────────────
try {
    // Own comments.
    $stmtComments = $myPDO->prepare('SELECT * FROM comments WHERE uid = :uid ORDER BY id DESC');
    $stmtComments->bindParam(':uid', $uid, PDO::PARAM_INT);
    $stmtComments->execute();
    $resCom = $stmtComments->fetchAll(PDO::FETCH_ASSOC);

    // All users for the sidebar.
    $stmtUsers = $myPDO->prepare('SELECT * FROM users ORDER BY user_id');
    $stmtUsers->execute();
    $allUsers = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

    // People this user follows.
    $stmtFollowing = $myPDO->prepare('
        SELECT f.*, u.userName, u.pic
          FROM following f
          JOIN users u ON u.user_id = f.following_id
         WHERE f.user_id = :profile_owner
    ');
    $stmtFollowing->execute([':profile_owner' => $uid]);
    $followingList = $stmtFollowing->fetchAll(PDO::FETCH_ASSOC);

    // Mutual follows = friends.
    $stmtFriends = $myPDO->prepare('
        SELECT f1.*, u.userName, u.pic
          FROM following f1
          JOIN following f2 ON f1.user_id    = f2.following_id
                           AND f1.following_id = f2.user_id
          JOIN users u ON u.user_id = f1.following_id
         WHERE f1.user_id = :profile_id
    ');
    $stmtFriends->execute([':profile_id' => $uid]);
    $friendsList = $stmtFriends->fetchAll(PDO::FETCH_ASSOC);

    // Does this user have at least one close friend?
    $stmtClose = $myPDO->prepare('
        SELECT 1 FROM following WHERE user_id = :uid AND close_friend = 1 LIMIT 1
    ');
    $stmtClose->execute([':uid' => $uid]);
    $hasCloseFriend = (bool)$stmtClose->fetch();

} catch (PDOException $e) {
    echo '<p>Error loading profile: '
       . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
}

$resCom        = $resCom        ?? [];
$allUsers      = $allUsers      ?? [];
$friendsList   = $friendsList   ?? [];
$followingList = $followingList ?? [];
?>

<header>
    <?php include '../assets/layouts/header.php'; ?>
</header>

<div class="background">

    <!-- ── Profile header ────────────────────────────────────────────────── -->
    <div class="profile flex">
        <?php
        try {
            $stmtProfile = $myPDO->prepare('SELECT userName, admin, banned, pic FROM users WHERE user_id = :uid');
            $stmtProfile->execute([':uid' => $uid]);
            $profile = $stmtProfile->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $profile = null;
        }

        if ($profile && $profile['banned'] !== 'y') {
            echo '<div class="flex">'
               . '<div class="profilePicContainer">'
               .   '<img src="' . $directory . htmlspecialchars($profile['pic'], ENT_QUOTES, 'UTF-8') . '" class="profilePic">'
               . '</div>'
               . '<div class="username"><h1>' . htmlspecialchars($profile['userName'], ENT_QUOTES, 'UTF-8') . '</h1></div>'
               . '<div class="status"><h3 class="filbut">you</h3></div>'
               . '</div>';

            if ($profile['admin'] === 'y') {
                echo '<div class="adminBadge"><h6>admin</h6></div>';
            }
        } elseif ($profile) {
            echo '<p>This account has been banned.</p>';
        } else {
            echo '<p>User not found.</p>';
        }
        ?>
    </div>

    <div class="flexDesk" id="alignment">

        <!-- ── Sidebar: section nav ──────────────────────────────────────── -->
        <aside class="filters">
            <h3 class="filbut center">filters</h3>
            <button class="filbut" onclick="location.href='#posts'">posts</button><br>
            <button class="filbut" onclick="location.href='#comments'">comments</button><br>
            <button class="filbut" onclick="location.href='#friends'">friends</button><br>
            <button class="filbut" onclick="location.href='#following'">following</button><br>
        </aside>

        <main>

            <!-- ── Posts ─────────────────────────────────────────────────── -->
            <div id="posts">
                <?php
                try {
                    $stmtPosts = $myPDO->prepare('SELECT * FROM posts WHERE user_id = :uid ORDER BY post_id DESC');
                    $stmtPosts->bindParam(':uid', $uid, PDO::PARAM_INT);
                    $stmtPosts->execute();
                    $result = $stmtPosts->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    $result = [];
                }

                if (empty($result)) {
                    echo '<p>No posts yet.</p>';
                } else {
                    foreach ($result as $row) {
                        $pid     = $row['post_id'];
                        $puser   = htmlspecialchars($row['puser'],   ENT_QUOTES, 'UTF-8');
                        $content = htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8');
                        $media   = htmlspecialchars($row['media']  ?? '', ENT_QUOTES, 'UTF-8');
                        $video   = htmlspecialchars($row['video']  ?? '', ENT_QUOTES, 'UTF-8');
                        $time    = htmlspecialchars($row['ptime'],   ENT_QUOTES, 'UTF-8');

                        echo '<div class="post">'
                           . '<div class="username"><h1>' . $puser . '</h1></div>'
                           . '<div class="message"><p class="large">'
                           . $content;

                        if ($media !== '') {
                            echo '<br><br><img id="postImage" src="' . $directory . $media . '">';
                        }

                        if ($video !== '') {
                            echo '<br><br><video height="150" controls>'
                               . '<source src="' . $directory . $video . '" type="video/mp4">'
                               . '</video>';
                        }

                        // Owner always sees delete on their own profile page.
                        echo '</p>'
                           . '<h6 class="inline">' . $time . '</h6>'
                           . '<a href="/project/deletePost.php?id=' . $pid . '">'
                           . '<h6 class="inline" style="margin-left:75%;">delete</h6></a>'
                           . '</div>' // .message
                           . '<div class="comments">'
                           . '<a href="/project/comments.php?id=' . $pid . '"><p>View comments</p></a>'
                           . '</div>'
                           . '</div>'; // .post
                    }
                }
                ?>
            </div>

            <!-- ── Comments ──────────────────────────────────────────────── -->
            <div id="comments">
                <?php foreach ($resCom as $row): ?>
                    <div class="comment">
                        <h4><?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p><?=  htmlspecialchars($row['content'],  ENT_QUOTES, 'UTF-8'); ?></p>
                        <h6><?= htmlspecialchars($row['ctime'],    ENT_QUOTES, 'UTF-8'); ?></h6>
                        <div class="comments">
                            <a href="/project/comments.php?id=<?= (int)$row['pid']; ?>">
                                <p>View post</p>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- ── Friends ───────────────────────────────────────────────── -->
            <div id="friends">
                <h2>Friends</h2>
                <div class="friendList" style="display:flex;flex-wrap:wrap;">
                    <?php if (empty($friendsList)): ?>
                        <p>No friends yet.</p>
                    <?php else: ?>
                        <?php foreach ($friendsList as $row): ?>
                            <a href="profiles.php?id=<?= (int)$row['user_id']; ?>">
                                <div class="friend">
                                    <img style="width:50px;height:50px;"
                                         src="<?= $directory . htmlspecialchars($row['pic'], ENT_QUOTES, 'UTF-8'); ?>"
                                         class="profilePic">
                                    <h3><?= htmlspecialchars($row['userName'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ── Following ─────────────────────────────────────────────── -->
            <div id="following">
                <h2>Following</h2>
                <div class="followingList" style="display:flex;flex-wrap:wrap;">
                    <?php if (empty($followingList)): ?>
                        <p>Not following anyone yet.</p>
                    <?php else: ?>
                        <?php foreach ($followingList as $row): ?>
                            <a href="profiles.php?id=<?= (int)$row['following_id']; ?>">
                                <div class="friend">
                                    <img style="width:50px;height:50px;"
                                         src="<?= $directory . htmlspecialchars($row['pic'], ENT_QUOTES, 'UTF-8'); ?>"
                                         class="profilePic">
                                    <h3><?= htmlspecialchars($row['userName'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </main>

        <!-- ── Sidebar: user list ────────────────────────────────────────── -->
        <aside>
            <?php foreach ($allUsers as $row):
                if ($row['banned'] === 'n'): ?>
                <div class="user">
                    <div class="inline">
                        <img src="<?= $directory . htmlspecialchars($row['pic'], ENT_QUOTES, 'UTF-8'); ?>"
                             style="width:50px;height:50px;overflow:hidden;">
                        <span class="inline">
                            <a href="profiles.php?id=<?= (int)$row['user_id']; ?>">
                                <h3><?= htmlspecialchars($row['userName'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            </a>
                        </span>
                        <?php if ($row['admin'] === 'y'): ?>
                            <span class="inline"><h6>admin</h6></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; endforeach; ?>
        </aside>

    </div>
</div><!-- .background -->

</body>
</html>
