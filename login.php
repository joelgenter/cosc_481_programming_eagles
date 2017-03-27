<?php
require 'db_connection.php';        //$conn (mysqli connection) is now available

//Form data
$firstName = filter_var ($_POST["fname"], FILTER_SANITIZE_STRING);
$lastName = filter_var ($_POST["lname"], FILTER_SANITIZE_STRING);
$username = filter_var ($_POST["username"], FILTER_SANITIZE_STRING);
$email = filter_var ($_POST["email"], FILTER_SANITIZE_EMAIL);


//SQl query
$query = "INSERT INTO ProteinSim.Users (username, firstName, lastName, email, type) VALUES (\"".$username."\", \"".$firstName."\", \"".$lastName."\", \"".$email."\", \"pending\");";
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (mysqli_multi_query($conn, $query)) {
    echo "New records created successfully";
} else {
    echo "Error: " . $query . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
// header("Location: index.html");
// die();
