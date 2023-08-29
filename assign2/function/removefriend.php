<?php
// Connect to the database
$host = "feenix-mariadb.swin.edu.au";
$user = "s103804535";
$pswd = "120403";
$dbnm = "s103804535_db";

$conn = mysqli_connect($host, $user, $pswd, $dbnm);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve the friend_id from the query string
$friend_id = $_GET['friend_id'];

// Get the friend_email and profile_name of the friend being removed
$sql = "SELECT friend_email, profile_name FROM friends WHERE friend_id=$friend_id";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);
$friend_email = $row['friend_email'];
$friend_name = $row['profile_name'];

// Get the friend_id of the logged-in user
session_start();
$email = $_SESSION['email'];
$sql = "SELECT friend_id FROM friends WHERE friend_email='$email'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);
$my_id = $row['friend_id'];

// Remove the friend from the 'myfriends' table
$sql = "DELETE FROM myfriends WHERE friend_id1=$my_id AND friend_id2=$friend_id";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Decrement the 'num_of_friends' column in the 'friends' table for both the logged-in user and the friend being removed
$sql = "SELECT num_of_friends FROM friends WHERE friend_id=$my_id";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);
$num_of_friends = $row['num_of_friends'] - 1;

$sql = "UPDATE friends SET num_of_friends=$num_of_friends WHERE friend_id=$my_id";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$sql = "SELECT num_of_friends FROM friends WHERE friend_id=$friend_id";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);
$num_of_friends = $row['num_of_friends'] - 1;

$sql = "UPDATE friends SET num_of_friends=$num_of_friends WHERE friend_id=$friend_id";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Redirect back to the 'friendlist.php' page
header("Location: ../friendlist.php");
exit();
?>