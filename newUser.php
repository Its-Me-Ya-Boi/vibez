<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vibez | New User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <!-- BUG FIX: was bare '../assets/dark.css' with no id or mobile sheet.
         Matched to the absolute paths used everywhere else in the project. -->
    <link rel="stylesheet" id="computerCSS" media="screen and (min-width:900px)" href="/project/assets/dark.css">
    <link rel="stylesheet" id="mobileCSS"   media="screen and (max-width:899px)"  href="/project/assets/darkMobile.css">
    <script src="/project/settings/script.js"></script>
</head>
<body>
<main>
    <form action="createUser.php" method="post" id="newUser">
        <!-- enctype="multipart/form-data" removed: no file upload on this form -->
        <label for="username">Username:</label>
        <input type="text"     name="username" id="username" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br>

        <label for="email">Email:</label>
        <input type="email"    name="email"    id="email"    required><br><br>

        <input type="submit" value="Create account" name="submit">
    </form>
</main>
</body>
</html>
