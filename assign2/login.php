<!DOCTYPE html>
<html>
<head>
    <title>Home Page</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <h1>Assignment 2 : Friends System</h1>
        <?php
            include('function/navigation.php');
        ?>
    </header>


    <?php

    // Check if user is already logged in. If true, redirect the user to friendlist.php
    if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
        header('Location: friendlist.php');
        exit;
    }

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // retrieve and sanitize user input
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];

        // Check if the user entered any data
        if (empty($email) || empty($password)) {
            $error = "Please enter your email address and password.";
        } else {
            // Define database connection.
            $host = "feenix-mariadb.swin.edu.au";
            $user = "s103804535"; 
            $pswd = "120403"; 
            $dbnm = "s103804535_db"; 

            // Connect to the database
            $conn = mysqli_connect($host, $user, $pswd, $dbnm);

            // Check if connection was successful
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // prepare the query with a prepared statement.
            $query = "SELECT * FROM friends WHERE friend_email=?";
            $stmt = mysqli_prepare($conn, $query);

            // Bind the parameters to the prepared statement
            mysqli_stmt_bind_param($stmt, "s", $email);

            // Execute the prepared statement
            mysqli_stmt_execute($stmt);

            // Get the result from the prepared statement
            $result = mysqli_stmt_get_result($stmt);

            // Check if query was successful
            if (!$result) {
                die("Query failed: " . mysqli_error($conn));
            }

            // Fetch the row from the result
            $row = mysqli_fetch_assoc($result);

            if ($row) {

                if (password_verify($password, $row['password'])) {
                    // Set session variables
                    $_SESSION['loggedIn'] = true;
                    $_SESSION['email'] = $email;
                    $_SESSION['profile_name'] = $row['profile_name'];

                    // Redirect to friendlist.php
                    header('Location: friendlist.php');
                    exit;
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Invalid email or password.";
            }

            // Close database connection
            mysqli_close($conn);
        }
    }


    // Define the sanitize function
    function sanitize($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return $data;
    }
    
    ?>

    <!-- Login Form -->
    <div class="container">
        <div class="box-container">
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <label for="email">Email address:</label>
                    <input type="text" name="email" id="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                <br>
                <label for="password">Password:</label>
                    <input type="password" name="password" id="password" value="<?php echo isset($error) ? '' : ''; ?>">
                <br>
                <input type="submit" value="Log In">
            </form>
        </div>
    </div>

    <div class="button-container">
        <p><a class="button" href="index.php">Back to Home</a></p>
    </div>
</body>
</html>