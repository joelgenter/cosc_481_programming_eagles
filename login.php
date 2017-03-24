<?php
require 'db_connection.php';        //$conn (mysqli connection) is now available

//Form data
$firstName = filter_var ($_POST["fname"], FILTER_SANITIZE_STRING);
$lastName = filter_var ($_POST["lname"], FILTER_SANITIZE_STRING);
$username = filter_var ($_POST["uname"], FILTER_SANITIZE_STRING);
$email = filter_var ($_POST["email"], FILTER_SANITIZE_EMAIL);


//SQl query
$sql = "INSERT INTO USERS (firstName, lastName, username, email, type)
        VALUES (\"".$firstName."\", \"".$lastName."\", \"".$username."\", \"".$email."\", 'pending')";


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (mysqli_multi_query($conn, $query)) {
    echo "New records created successfully";
} else {
    echo "Error: " . $query . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
header("Location: queue.php");
die();
