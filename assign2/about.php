<!DOCTYPE html>
<html>
<head>
    <title>Home Page</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <header>
        <h1>Assignment 2 : Friends System</h1>
        <?php
            include('function/navigation.php');
        ?>
    </header>

    <div class="container">
            <div class="box-container">
                <ul class = "info">
                    <li><b><i><u>The php version of Mercury is:</b></i></u> <?php echo phpversion(); ?></li>
                    <br>
                    <li><b><i><u>What tasks you have not attempted or not completed? </b></i></u></li>
                    <li>I believed that I have finished all of the tasks given and the extend tasks.</li>
                    <br>
                    <li><b><i><u>What special features and additional features have you done, or attempted, in creating the site that we should know about?</b></i></u></li>
                    <li><b>Hashing password: </b> For increased security, I have implemented password hashing before storing user passwords in the database. This means that when a user registers an account and chooses a password, that password is not saved directly in the database. Instead, a one-way hashed version of the password is generated and stored. Then, when the user attempts to log in, their entered password is hashed and compared to the stored hashed password. This ensures that if the database was ever compromised, the actual passwords would still be secure.</li>
                    <li><b>Responsive navigation bar and index buttons: </b> The navigation bar will vary depending on whether the user has registered or logged in to the website. If the user has already logged in with their account, the login and sign-up buttons will be replaced with a user menu and a logout button. Similar changes will also apply to the controls for the Home and About page.</li>
                    <br>
                    <li><b><i><u>Which parts did you have trouble with? </b></i></u></li>
                    <li>I think most of the trouble came from implementing my special features like hasing passwords since it's requires alot of fixing and modifying my existing code, which leads to alot of errors and bugs in the process. Also the friendlist adding functions cause a little trouble for me but it has been solved with changing the SQL queries.</li>
                    <br>
                    <li><b><i><u>What would you like to do better next time? </b></i></u></li>
                    <li>I would want to improve the SQL and adding some better XSS and SQL injection methods to the website for further security next time. Also improving the interface of the website.</li>
                    <br>
                    <li><b><i><u>Screenshots of discussion responses that answered: </b></i></u></li>
                    <li><b>Discussion Week 6: Can you do the rotated array problem ? </b></li>
                    <img src="Pictures/discussion6.png" alt="Discussion 6" width="100%d">
                    <br>
                    <li><b>w10 Discussion : The use of destructor and magic methods </b></li>
                    <img src="Pictures/discussion10.png" alt="Discussion 10" width="100%d">
                </ul>
            </div>
            <div class="button-container">
                <?php
                    // display the appropriate button based on whether the user is logged in or not
                    if (isset($_SESSION['profile_name'])) {
                        echo "<a href='friendlist.php' class='button'>View Friends</a>";
                        echo "<a href='friendadd.php' class='button'>Add Friends</a>";
                        echo "<p><a href='logout.php' class='button'>Log out</a></p>";
                    } else {
                        echo "<a href='login.php' class='button'>Log in</a>";
                        echo "<p><a href='signup.php' class='button'>Sign up</a></p>";
                    }
                ?>
                <p><a href="about.php" class="button">About</a></p>
            </div>
    </div>