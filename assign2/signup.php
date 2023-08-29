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

    <?php

        // Define database connection.
        $host = "feenix-mariadb.swin.edu.au";
        $user = "s103804535"; 
        $pswd = "120403"; 
        $dbnm = "s103804535_db"; 

        // Connect to the database
        $conn = mysqli_connect($host, $user, $pswd, $dbnm);

        // Define the sanitize function
        function sanitize($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return $data;
        }

        // Handle form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            // Retrieve form data and validate
            $email = mysqli_real_escape_string($conn, sanitize($_POST["email"]));
            $profile_name = mysqli_real_escape_string($conn, sanitize($_POST["profile_name"]));
            $password = mysqli_real_escape_string($conn, sanitize($_POST["password"]));
            $confirm_password = mysqli_real_escape_string($conn, sanitize($_POST["confirm_password"]));

            $errors = [];

            if (empty($email)) {
                $errors["email"] = "Email is required";
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors["email"] = "Invalid email format";
            } else {
                // Check if email already exists in friends table
                $query = "SELECT * FROM friends WHERE friend_email='$email'";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    $errors["email"] = "Email already exists";
                }
            }

            if (empty($profile_name)) {
                $errors["profile_name"] = "Profile name is required";
            } else if (!preg_match("/^[a-zA-Z ]*$/", $profile_name)) {
                $errors["profile_name"] = "Profile name must contain only letters and spaces";
            }

            if (empty($password)) {
                $errors["password"] = "Password is required";
            } else if (!preg_match("/^(?=.*\d)(?=.*[A-Z])(?=.*\W).{8,}$/", $password)) {
                $errors["password"] = "Password must contain at least 1 number, 1 uppercase letter, and 1 special character and be at least 8 characters long";
            }

            if (empty($confirm_password)) {
                $errors["confirm_password"] = "Confirm password is required";
            } else if ($password !== $confirm_password) {
                $errors["confirm_password"] = "Passwords do not match";
            }

            // If there are no errors, insert data into friends table
            if (count($errors) == 0) {
                $date_started = date("Y-m-d");
                $num_of_friends = 0;

                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                $query = "INSERT INTO friends (friend_email, password, profile_name, date_started, num_of_friends)
                    VALUES ('$email', '$hashed_password', '$profile_name', '$date_started', '$num_of_friends')";

                if (mysqli_query($conn, $query)) {
                    // Set session variables and redirect to friendadd.php
                    $_SESSION["email"] = $email;
                    $_SESSION["profile_name"] = $profile_name;
                    header("Location: friendadd.php");
                    exit();
                } else {
                    $errors["database"] = "Error: " . mysqli_error($conn);
                }
            } else {
                // Set session variables for profile name and email
                $_SESSION["email"] = $email;
                $_SESSION["profile_name"] = $profile_name;
            }
        }

    ?>

    <!-- Signup Form with invalid messages -->
    <div class="container">
        <div class="box-container">
            <h2 style="margin-bottom:10px;">Registration Page</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" value="<?php echo isset($_SESSION["email"]) ? $_SESSION["email"] : ""; ?>">
                <?php if (isset($errors["email"])) { ?>
                    <span class="error"><?php echo $errors["email"]; ?></span>
                <?php } ?>
                <br>

                <label for="profile_name">Profile Name:</label>
                <input type="text" id="profile_name" name="profile_name" value="<?php echo isset($_SESSION["profile_name"]) ? $_SESSION["profile_name"] : ""; ?>">
                <?php if (isset($errors["profile_name"])) { ?>
                    <span class="error"><?php echo $errors["profile_name"]; ?></span>
                <?php } ?>
                <br>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password">
                <?php if (isset($errors["password"])) { ?>
                    <span class="error"><?php echo $errors["password"]; ?></span>
                <?php } ?>
                <br>

                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password">
                <?php if (isset($errors["confirm_password"])) { ?>
                    <span class="error"><?php echo $errors["confirm_password"]; ?></span>
                <?php } ?>
                <br>

                <input type="submit" value="Register">

                <!-- Clear button -->
                <input type="button" value="Clear" onclick="location.href='<?php echo $_SERVER["PHP_SELF"] . '?clear=true'; ?>'">
                <?php
                if (isset($_GET["clear"]) && $_GET["clear"] == "true") {
                    unset($_SESSION["email"]);
                    unset($_SESSION["profile_name"]);
                    header("Location: " . $_SERVER["PHP_SELF"]);
                    exit();
                }
                ?>
            </form>
        </div>

    <?php mysqli_close($conn); ?>
</body>
</html>