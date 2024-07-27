<?php
function navBar() {
?>
  <div class="navbar navbar-expand-lg navbar-dark topnav">
    <div class="container">
      <a class="navbar-brand" href="index.php">
            <img src="images/logo.svg" alt="logo" style="width: 80%; max-width: 55px;">
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.php" style="z-index: 100; color: #ffffff; font-family: Arial, Helvetica, sans-serif;"><i class="fas fa-home"></i> Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="music.php" style="z-index: 100; color: #ffffff; font-family: Arial, Helvetica, sans-serif;"><i class="fas fa-music"></i> Music</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="news.php " style="z-index: 100; color: #ffffff; font-family: Arial, Helvetica, sans-serif;"><i class="fas fa-newspaper"></i> News</a>
          </li>
          <!-- <li class="nav-item">
            <a class="nav-link" href="#" style="z-index: 100; color: #ffffff; font-family: Arial, Helvetica, sans-serif;"><i class="fas fa-video"></i> Videos</a>
          </li> -->
        </ul>
        <form class="form-inline my-2 my-lg-0 search-form" action="search.php">
          <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="query">
          <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Search</button>
        </form>
      </div>
    </div>
</div>


<?php
}
?>