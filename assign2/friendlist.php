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
			<h2 style="margin-bottom: 5px;">User Profile</h2>
			<?php
			// check if the user is logged in
			if (isset($_SESSION['profile_name'])) {
				// display the user's information
				echo "<ul class='info'>";
				echo "<li><b>Name: </b>" . htmlspecialchars($_SESSION['profile_name'], ENT_QUOTES, 'UTF-8') . "</li>";
				echo "<li><b>Email: </b>" . htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8') . "</li>";
				echo "</ul>";
			} else {
				// display a message asking the user to log in
				echo "<p>Please log in to view your profile.</p>";
			}
			?>      

			<?php

			// Check if the user is logged in
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

			// Retrieve the profile name of the logged-in user
			$email = $_SESSION['email'];
			$sql = "SELECT profile_name FROM friends WHERE friend_email='$email'";
			$result = mysqli_query($conn, $sql);
			$row = mysqli_fetch_assoc($result);
			$profile_name = $row['profile_name'];
			?>

			<h2 style="margin-bottom: 5px;"><?php echo "$profile_name's Friend List Page"; ?></h2>

			<?php
			// Retrieve the list of friends for the logged-in user
			$sql = "SELECT friends.friend_id, friends.profile_name FROM friends, myfriends WHERE friends.friend_id=myfriends.friend_id2 AND myfriends.friend_id1=(SELECT friend_id FROM friends WHERE friend_email='$email')";
			$result = mysqli_query($conn, $sql);
			
			// Display the list of friends on the page as a table
			echo "<p style='margin-bottom: 3px;'>Total number of friends is " . mysqli_num_rows($result) . "</p>";
			echo "<table>";
			echo "<thead><tr><th>Friend Name</th><th>Total Friends</th><th>Action</th></tr></thead>";
			echo "<tbody>";
			while ($row = mysqli_fetch_assoc($result)) {
				$friend_id = $row['friend_id'];
				$friend_name = $row['profile_name'];
				$total_friends = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM myfriends WHERE friend_id1=$friend_id"));
				echo "<tr>
					<td>$friend_name</td>
					<td>$total_friends</td>
					<td><a class='unfriend-button' href='function/removefriend.php?friend_id=$friend_id'><img src='./Pictures/Unfriend.png' alt='Unfriend' class='unfriend-icon'></a></td>
				</tr>";
			}
			echo "</tbody>";
			echo "</table>";
			?>
		</div>
	</div>
	<div class="button-container">
		<p><a class="button" href='friendadd.php'>Add Friends</a> 
		<a class="button" href='logout.php'>Log out</a> 
		<a class="button" href='index.php'>Back to Home Page</a></p>
	</div>
	<footer>
    	<p>&copy; 2023 Huynh Nguyen Quoc Bao - 103804535</p>
	</footer>
</body>
</html>