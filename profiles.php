<?php
/**
 * profiles.php — Public profile page for any user.
 *                Shows their posts, comments, friends, and following list.
 *
 * Expects: GET['id'] — the profile owner's user ID (integer).
 * Lives at: /project/users/profiles.php
 */

session_start();

// ── Auth guard ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['un']) || !isset($_SESSION['uid'])) {
    header('Location: /project/login.php');
    exit();
}

$user = $_SESSION['un'];           // logged-in username
$uid  = (int)$_SESSION['uid'];     // logged-in user ID

// Validate the profile ID from the query string.
// BUG FIX (error_log): $fid was never validated — fatal "Call to member function
//         fetch() on null" when id was missing or non-numeric.
if (!isset($_GET['id']) || !ctype_digit($_GET['id']) || (int)$_GET['id'] === 0) {
    header('Location: /project/main.php');
    exit();
}

$fid = (int)$_GET['id'];   // profile being viewed

// ── Bootstrap ────────────────────────────────────────────────────────────────
require('../../../vibes.php');

// ── Load profile theme colours ────────────────────────────────────────────────
try {
    $stmtbg = $myPDO->prepare('SELECT * FROM profiles WHERE usr_id = :uid');
    $stmtbg->bindParam(':uid', $fid, PDO::PARAM_INT);
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
    // Default dark-mode palette.
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
        .status {
            width: 60%;
            display: flex;
            align-items: flex-end;
        }
        .profileHeader { display: flex; width: 100%; }
        .followBadge {
            background-color: <?= $bcolor; ?>;
            color: <?= $tcolor; ?>;
            padding: 3px 10px;
            border-radius: 5px;
            font-size: 14px;
            margin-left: 10px;
            width: fit-content;
        }
        a, .comments a, .friend a, .status a, .username a, .filbut a { color: <?= $lcolor; ?>; }
    </style>
</head>
<body>

<?php
$directory = '/project/assets/images/';

// ── Data queries ──────────────────────────────────────────────────────────────
try {
    // Comments made by the profile owner.
    $stmtComments = $myPDO->prepare('SELECT * FROM comments WHERE uid = :uid ORDER BY id DESC');
    $stmtComments->bindParam(':uid', $fid, PDO::PARAM_INT);
    $stmtComments->execute();
    $resCom = $stmtComments->fetchAll(PDO::FETCH_ASSOC);

    // All users for the sidebar.
    $stmtUsers = $myPDO->prepare('SELECT * FROM users ORDER BY user_id');
    $stmtUsers->execute();
    $allUsers = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

    // People the profile owner follows.
    $stmtFollowing = $myPDO->prepare('
        SELECT f.*, u.userName, u.pic
          FROM following f
          JOIN users u ON u.user_id = f.following_id
         WHERE f.user_id = :profile_owner
    ');
    $stmtFollowing->execute([':profile_owner' => $fid]);
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
    $stmtFriends->execute([':profile_id' => $fid]);
    $friends = $stmtFriends->fetchAll(PDO::FETCH_ASSOC);

    // Is the profile owner a close friend of the logged-in user?
    $stmtClose = $myPDO->prepare('
        SELECT * FROM following
         WHERE user_id = :uid AND following_id = :fid AND close_friend = 1
    ');
    $stmtClose->execute([':uid' => $uid, ':fid' => $fid]);
    $isClose     = (bool)$stmtClose->fetch(PDO::FETCH_ASSOC);
    // BUG FIX: original ran the same query twice to get $isClose and $closeFriend
    //          separately. $isClose is a bool for the badge; reuse it.
    $closeFriend = $isClose;

} catch (PDOException $e) {
    echo '<p>Error loading profile data: '
       . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
}

$resCom        = $resCom        ?? [];
$allUsers      = $allUsers      ?? [];
$friends       = $friends       ?? [];
$followingList = $followingList ?? [];
?>

<header>
    <?php include '../assets/layouts/header.php'; ?>
</header>

<div class="background">

    <!-- ── Profile header ────────────────────────────────────────────────── -->
    <div class="profile flex">
        <?php
        // Fetch this profile's user record.
        $stmtProfile = $myPDO->prepare('SELECT userName, admin, banned, pic FROM users WHERE user_id = :uid');
        $stmtProfile->execute([':uid' => $fid]);
        $profile = $stmtProfile->fetch(PDO::FETCH_ASSOC);

        // Does the logged-in user follow this profile?
        // BUG FIX (error_log): $following was fetching ALL rows the logged-in user
        //         follows and casting to bool — it was always true if they follow anyone.
        //         Now correctly checks if $uid follows $fid specifically.
        $stmtFollowCheck = $myPDO->prepare('
            SELECT 1 FROM following WHERE user_id = :uid AND following_id = :fid
        ');
        $stmtFollowCheck->execute([':uid' => $uid, ':fid' => $fid]);
        $following = (bool)$stmtFollowCheck->fetch();

        // Does this profile follow the logged-in user back?
        $stmtFollowedCheck = $myPDO->prepare('
            SELECT 1 FROM following WHERE user_id = :fid AND following_id = :uid
        ');
        $stmtFollowedCheck->execute([':uid' => $uid, ':fid' => $fid]);
        $followed = (bool)$stmtFollowedCheck->fetch();

        if ($profile) {
            if ($profile['banned'] !== 'y') {
                echo '<div class="flex profileHeader">'
                   . '<div class="profilePicContainer">'
                   .   '<img src="' . $directory . htmlspecialchars($profile['pic'], ENT_QUOTES, 'UTF-8') . '" class="profilePic">'
                   . '</div>'
                   . '<div class="username"><h1>' . htmlspecialchars($profile['userName'], ENT_QUOTES, 'UTF-8') . '</h1></div>'
                   . '<div class="status">';

                if ($profile['userName'] === $user) {
                    // Viewing your own profile.
                    echo '<h3 class="filbut followBadge">you</h3>';
                } elseif ($following && $followed) {
                    // Mutual follow = friend.
                    echo '<a href="follow.php?id=' . $fid . '"><h3 class="filbut followBadge">friend</h3></a>';
                    if ($isClose) {
                        echo '<a href="closeFriend.php?id=' . $fid . '"><h3 class="filbut followBadge" style="margin-left:10px;">close friend ✓</h3></a>';
                    } else {
                        echo '<a href="closeFriend.php?id=' . $fid . '"><h3 class="filbut followBadge" style="margin-left:10px;">add close friend</h3></a>';
                    }
                } elseif ($following) {
                    echo '<a href="follow.php?id=' . $fid . '"><h3 class="filbut followBadge">following</h3></a>';
                } else {
                    echo '<a href="follow.php?id=' . $fid . '"><h3 class="filbut followBadge">follow</h3></a>';
                }

                echo '</div></div>';

                if ($profile['admin'] === 'y') {
                    echo '<div class="adminBadge"><h6>admin</h6></div>';
                }
            } else {
                echo '<p>This account has been banned.</p>';
            }
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
            <button class="filbut" onclick="location.href='#close'">close friends</button><br>
            <button class="filbut" onclick="location.href='#friends'">friends</button><br>
            <button class="filbut" onclick="location.href='#following'">following</button><br>
        </aside>

        <main>

            <!-- ── Posts ─────────────────────────────────────────────────── -->
            <div id="posts">
                <?php
                try {
                    $stmtPosts = $myPDO->prepare('SELECT * FROM posts WHERE user_id = :uid ORDER BY post_id DESC');
                    $stmtPosts->bindParam(':uid', $fid, PDO::PARAM_INT);
                    $stmtPosts->execute();
                    $result = $stmtPosts->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo '<p>Could not load posts: '
                       . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
                    $result = [];
                }

                if (empty($result)) {
                    echo '<p>No posts found for this user.</p>';
                } else {
                    foreach ($result as $row) {
                        $usrid   = $row['user_id'];
                        $poid    = $row['post_id'];
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

                        echo '</p>'
                           . '<h6 class="inline">' . $time . '</h6>';

                        // Only the post owner sees a delete link.
                        if ($uid === $usrid) {
                            echo '<a href="/project/deletePost.php?id=' . $poid . '">'
                               . '<h6 class="inline" style="margin-left:75%;">delete</h6></a>';
                        }

                        echo '</div>'; // .message
                        echo '<div class="comments">'
                           . '<a href="/project/comments.php?id=' . $poid . '"><p>View comments</p></a>'
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

            <!-- ── Close friends ─────────────────────────────────────────── -->
            <?php if ($closeFriend): ?>
                <div id="close">
                    <h2>Close Friends</h2>
                    <div style="display:flex;flex-wrap:wrap;">
                        <?php foreach ($followingList as $row):
                            if (isset($row['close_friend']) && $row['close_friend'] == 1): ?>
                                <a href="/project/users/profiles.php?id=<?= (int)$row['following_id']; ?>">
                                    <div class="friend">
                                        <img style="width:50px;height:50px;"
                                             src="<?= $directory . htmlspecialchars($row['pic'], ENT_QUOTES, 'UTF-8'); ?>"
                                             class="profilePic">
                                        <h3><?= htmlspecialchars($row['userName'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    </div>
                                </a>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ── Friends ───────────────────────────────────────────────── -->
            <div id="friends">
                <h2>Friends</h2>
                <div class="friendList" style="display:flex;flex-wrap:wrap;">
                    <?php if (empty($friends)): ?>
                        <p>This user has no friends yet.</p>
                    <?php else: ?>
                        <?php foreach ($friends as $row): ?>
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
                        <p>This user is not following anyone yet.</p>
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
