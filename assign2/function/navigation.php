<nav>
  <ul>
    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?> right-border"><a href="index.php">Home</a></li>
    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?> right-border"><a href="about.php">About</a></li>
    <?php
    // start the session
    session_start();
    if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true){
        if (isset($_SESSION["email"]) && isset($_SESSION["profile_name"])) {
            // display the user's profile name and logout button
            echo "<li class='profile'><div class='profile-box'><span class='profile-name'> Logged in to User: <b><a class='profilenamebutton' href = 'friendlist.php'>" . $_SESSION['profile_name'] . "</a></b></span><a href='logout.php' class='logout-button'><b>Logout</b></a></div></li>";
        } else {
            // display the "Login" and "Sign Up" buttons
            echo "<li><a href='login.php'>Login</a></li>";
            echo "<li><a href='signup.php'>Sign Up</a></li>";
        }
    } else {
        // display the "Login" and "Sign Up" buttons
        echo "<li><a href='login.php'>Login</a></li>";
        echo "<li><a href='signup.php'>Sign Up</a></li>";
    }
    ?>
  </ul>
</nav>