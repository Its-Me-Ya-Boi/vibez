<?php
/**
 * comments.php (guest) — Read-only comment view, no session required.
 * Lives at: /project/guest/comments.php
 */

require('../../../vibes.php');

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die('Invalid post ID.');
}

$postId = (int)$_GET['id'];

try {
    $stmt = $myPDO->prepare('SELECT * FROM comments WHERE pid = :id ORDER BY id DESC');
    $stmt->bindParam(':id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $results = [];
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
</head>
<body>

<header>
    <h1 class="center">Guest mode</h1>
    <a href="/project/login.php"><h3 class="center">Login to view more</h3></a>
</header>

<main>
    <?php foreach ($results as $row): ?>
        <div class="comment">
            <h4><?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></h4>
            <p><?=  htmlspecialchars($row['content'],  ENT_QUOTES, 'UTF-8'); ?></p>
            <br>
            <h6 class="inline"><?= htmlspecialchars($row['ctime'], ENT_QUOTES, 'UTF-8'); ?></h6>
        </div>
    <?php endforeach; ?>
</main>

</body>
</html>
