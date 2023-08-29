<!DOCTYPE html>
<html>
<head>
    <title>Assignment 2 : Friends System</title>
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
            <h2 style="margin-bottom: 5px;">Add Friends</h2>

            <?php
            if (!isset($_SESSION['email'])) {
                header("Location: login.php");
                exit();
            }

            // Connect to the database
            $host = "feenix-mariadb.swin.edu.au";
            $user = "s103804535";
            $pswd = "120403";
            $dbnm = "s103804535_db";

            $conn = mysqli_connect($host, $user, $pswd, $dbnm);

            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Retrieve the profile name and friend ID of the logged-in user
            $email = $_SESSION['email'];
            $sql = "SELECT friend_id, profile_name FROM friends WHERE friend_email='$email'";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $profile_name = $row['profile_name'];
            $friend_id = $row['friend_id'];

            // Set the page number
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

            // Retrieve the count of registered users who are not already friends of the logged-in user
            $sqlCountUsers = "SELECT COUNT(*) AS row_count
                    FROM friends f 
                    WHERE f.friend_email != '$email' 
                        AND f.friend_id NOT IN (SELECT friend_id2 FROM myfriends WHERE friend_id1 = $friend_id)";
            $resultCountUsers = mysqli_query($conn, $sqlCountUsers);
            $rowCountUsers = mysqli_fetch_assoc($resultCountUsers);
            $row_count = $rowCountUsers['row_count'];

            // Calculate the number of pages needed
            $pages_needed = ceil($row_count / 5);

            // If the requested page is greater than the number of pages needed, redirect to the last page
            if ($page > $pages_needed) {
                header("Location: friendadd.php?page=$pages_needed");
                exit();
            }

            // Calculate the offset for the current page
            $offset = ($page - 1) * 5;

            // Retrieve the list of registered users who are not already friends of the logged-in user for the current page
            $sqlLimitUsers = "SELECT f.friend_id, f.profile_name, 
                    (SELECT COUNT(DISTINCT m1.friend_id2, m2.friend_id2) 
                        FROM myfriends m1 
                        JOIN myfriends m2 ON m1.friend_id2 = m2.friend_id2 
                        WHERE m1.friend_id1 = $friend_id AND m2.friend_id1 = f.friend_id
                    ) AS mutual_friends 
                    FROM friends f 
                    WHERE f.friend_email != '$email' 
                        AND f.friend_id NOT IN (SELECT friend_id2 FROM myfriends WHERE friend_id1 = $friend_id) 
                    ORDER BY mutual_friends DESC, f.profile_name ASC 
                    LIMIT 5 OFFSET $offset";
            $resultLimitUsers = mysqli_query($conn, $sqlLimitUsers);

            $total_friends = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM myfriends WHERE friend_id1=$friend_id"));
            echo "<p style='margin-bottom: 3px;'>Total number of friends is $total_friends</p>";

            // Redirect back to the previous page if the current page is not needed
            if (mysqli_num_rows($resultLimitUsers) == 0 && $page > 1) {
                header("Location: friendadd.php?page=" . ($page - 1));
                exit;
            }

            // Display the list of registered users on the page
            if (mysqli_num_rows($resultLimitUsers) > 0) {
                // Display the list of registered users on the page
                echo "<table>";
                echo "<thead>
                        <tr>
                            <th>Profile Name</th>
                            <th>Mutual Friends</th>
                            <th>Action</th>
                        </tr>
                        </thead>";
                while ($row = mysqli_fetch_assoc($resultLimitUsers)) {
                    $friend_id = $row['friend_id'];
                    $friend_name = $row['profile_name'];
                    $mutual_friends = $row['mutual_friends'];
                    echo "<tr>
                            <td>$friend_name</td>
                            <td>$mutual_friends</td>
                            <td><a class='addfriend-button' href='function/addfriendaction.php?friend_id=$friend_id'><img src='./Pictures/Addfriend.png' class='addfriend-icon'></a></td>
                            </tr>";
                }
                echo "</table>";
            } else {
                // Display a message indicating that there are no friends to display
                echo "<p>There are no registered users who are not already your friends.</p>";
                echo "<p><a href='friendadd.php?page=1'>Go back to page 1</a></p>";
            }

            // Display the pagination links
            echo "<div class='pagination-container'>";
            echo "<ul class='pagination'>";

            // Display the "Previous" link if the user is not on the first page
            if ($page > 1) {
                echo "<li><a href='friendadd.php?page=".($page-1)."'>Previous</a></li>";
            }

            // Display the page links
            for ($i = 1; $i <= $pages_needed; $i++) {
                if ($i == $page) {
                    echo "<li class='active'><a href='friendadd.php?page=$i'>$i</a></li>";
                } else {
                    echo "<li><a href='friendadd.php?page=$i'>$i</a></li>";
                }
            }

            // Display the "Next" link if the user is not on the last page
            if ($page < $pages_needed) {
                echo "<li><a href='friendadd.php?page=".($page+1)."'>Next</a></li>";
            }

            echo "</ul>";
            echo "</div>";

            // Close the database connection
            mysqli_close($conn);
            ?>
        </div>
    </div>
    <div class="button-container">
		<a class="button" href='friendlist.php'>Friend List</a> 
		<a class="button" href='logout.php'>Log out</a> 
		<a class="button" href='index.php'>Back to Home Page</a>
	</div>
	<footer>
    	<p>&copy; 2023 Huynh Nguyen Quoc Bao - 103804535</p>
	</footer>
</body>
</html>