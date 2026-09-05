
            <div class="left"><a href="/project/main.php"><img id="logo" src="/project/assets/images/vibezNight.png" style="width: 100px; height: auto;"></a></div>
            
            <div class="flex">
              <div class="search">
                <form action="/project/filter/search.php" method="get">
                    <input class="search-input" type="text" name="query" placeholder="Search...">
                    <button class="search-button" type="submit">Search</button>
                </form>
              </div>
            <div class="dropdown">
        <button class="dropbtn">post</button>
        <div class="dropdown-content">
        <a href="/project/posting/post.php">post</a>
        <a href="/project/posting/media.php">post w/ image</a>
        <a href="/project/posting/video.php">post w/ video</a>
  </div>
</div> 
             <div class="dropdown">
        <button class="dropbtn">personal</button>
        <div class="dropdown-content">
        <?php
        echo '<a href="/project/users/profile.php?id=' .$uid. '">profile</a>' ?>
        <a href="/project/settings/set.php">settings</a>
        <a href="/project/logout.php">log out</a>
  </div>
</div> 
            </div>
