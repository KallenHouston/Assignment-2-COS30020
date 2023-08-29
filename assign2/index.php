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

    <!-- Content -->
    <div class="container">
            <div class="box-container">
                <ul class = "info">
                    <li><b>Name: </b>Bao Huynh Nguyen Quoc</li>
                    <li><b>Student ID: </b>103804535</li>
                    <li><b>Email: </b>103804535@swin.edu.au</li>
                    <li>I declare that this assignment is my individual work. I have not worked collaboratively nor have I copied from any other students work or from any other source.</li>
                </ul>
            </div>
            <div class="button-container">
                <?php
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

    <!-- Database setup -->
    <?php
    // Define database connection.
    $host = "feenix-mariadb.swin.edu.au";
    $user = "s103804535"; 
    $pswd = "120403"; 
    $dbnm = "s103804535_db"; 

    // Connect to the database
    $conn = mysqli_connect($host, $user, $pswd, $dbnm);

    // Create the 'friends' table if it does not exist
    if (mysqli_query($conn, "CREATE TABLE IF NOT EXISTS friends (
        friend_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        friend_email VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL,
        profile_name VARCHAR(30) NOT NULL,
        date_started DATE NOT NULL,
        num_of_friends INT(10) UNSIGNED
    )")) {
        echo "<div class='content success'><p>Table friends created successfully</p></div>";
    } else {
        echo "<div class='content error'><p>Error creating table: </p>" . mysqli_error($conn) . "</div>";
    }

     // Reset the auto-increment counter for the 'friends' table when a new table created.
    mysqli_query($conn, "ALTER TABLE friends AUTO_INCREMENT = 1");

    // Add sample data to the 'friends' table if it is empty, also hashing the password.
    $result = mysqli_query($conn, "SELECT COUNT(*) FROM friends");
    $row = mysqli_fetch_row($result);
    if ($row[0] == 0) {
        $friends_query = "INSERT INTO friends (friend_email, password, profile_name, date_started) VALUES 
        ('john@example.com', '".password_hash('password1', PASSWORD_BCRYPT)."', 'John', '2021-01-01'),
        ('jane@example.com', '".password_hash('password2', PASSWORD_BCRYPT)."', 'Jane', '2021-02-01'),
        ('bob@example.com', '".password_hash('password3', PASSWORD_BCRYPT)."', 'Bob', '2021-03-01'),
        ('alice@example.com', '".password_hash('password4', PASSWORD_BCRYPT)."', 'Alice', '2021-04-01'),
        ('dave@example.com', '".password_hash('password5', PASSWORD_BCRYPT)."', 'Dave', '2021-05-01'),
        ('emma@example.com', '".password_hash('password6', PASSWORD_BCRYPT)."', 'Emma', '2021-06-01'),
        ('fred@example.com', '".password_hash('password7', PASSWORD_BCRYPT)."', 'Fred', '2021-07-01'),
        ('grace@example.com', '".password_hash('password8', PASSWORD_BCRYPT)."', 'Grace', '2021-08-01'),
        ('hank@example.com', '".password_hash('password9', PASSWORD_BCRYPT)."', 'Hank', '2021-09-01'),
        ('ivy@example.com', '".password_hash('password10', PASSWORD_BCRYPT)."', 'Ivy', '2021-10-01')
        ";

        if (mysqli_query($conn, $friends_query)) {
            echo "<div class='content success'><p>Users added successfully</p></div>";
        } else {
            echo "<div class='content error'><p>Error adding users: </p>" . mysqli_error($conn) . "</div>";
        }
    }

    // Create the 'myfriends' table if it does not exist
    if(mysqli_query($conn, "CREATE TABLE IF NOT EXISTS myfriends (
        friend_id1 INT(10) UNSIGNED NOT NULL,
        friend_id2 INT(10) UNSIGNED NOT NULL,
        PRIMARY KEY (friend_id1, friend_id2),
        FOREIGN KEY (friend_id1) REFERENCES friends(friend_id),
        FOREIGN KEY (friend_id2) REFERENCES friends(friend_id),
        CHECK (friend_id1 <> friend_id2)

    )")) {
        echo "<div class='content success'><p>The myfriends table has been successfully created.</p></div>";

    } else {
        echo "<div class='content error'><p>Error creating the myfriends table: " . mysqli_error($conn) . "</p></div>";
    }

    // Reset the auto-increment counter for the 'myfriends' table when a new table created.
    mysqli_query($conn, "ALTER TABLE myfriends AUTO_INCREMENT = 1");

    // Add sample data to the 'myfriends' table if it is empty, also hashing the password.
    $result = mysqli_query($conn, "SELECT COUNT(*) FROM myfriends");
    $row = mysqli_fetch_row($result);
    if ($row[0] == 0) {
        $myfriends_query = "INSERT IGNORE INTO myfriends (friend_id1, friend_id2) VALUES
        (1, 2), (1, 3), (1, 4),
        (2, 3), (2, 4), (2, 5),
        (3, 4), (3, 5),
        (4, 5), (4, 6),
        (5, 6), (5, 7), 
        (6, 7), (6, 8),
        (7, 8), (7, 9),
        (8, 9), (8, 10),
        (9, 10)
        ";

        if (mysqli_query($conn, $myfriends_query)) {
            echo "<div class='content success'><p>MyFriends added successfully</p></div>";
        } else {
            echo "<div class='content error'><p>Error adding MyFriends: " . mysqli_error($conn) . "</p></div>";
        }
    }

    // SQL query to update the 'num_of_friends' column in the 'friends' table.
    $query = "
        UPDATE friends f
        LEFT JOIN (
            SELECT friend_id1 as friend_id, COUNT(*) as count
            FROM myfriends
            GROUP BY friend_id1
            UNION ALL
            SELECT friend_id2 as friend_id, COUNT(*) as count
            FROM myfriends
            GROUP BY friend_id2
        ) mf ON f.friend_id = mf.friend_id
        SET f.num_of_friends = IFNULL(mf.count, 0)
    ";
    // Execute the query and store the result in $result.
    $result = mysqli_query($conn, $query);

    // Display a success message if the query was successful, or an error message with the error details if it was not.
    if ($result) {
        echo "<div class='content success'><p>The num_of_friends column in the friends table has been updated.</p></div>";
    } else {
        echo "<div class='content error'><p>Error updating the num_of_friends column: " . mysqli_error($conn) . "</p></div>";
    }

    //close the connection.
    mysqli_close($conn);
?>

    <footer>
        <p>&copy; 2023 Huynh Nguyen Quoc Bao - 103804535</p>
    </footer>
</body>
</html>