<?php
require 'db_connection.php';        //$conn (mysqli connection) is now available

$username = $_POST['username'];
$userType = $_POST['userType'];

$query = "UPDATE ProteinSim.Users SET type = '$userType' WHERE username = '$username'";
if ($stmt = $conn->prepare($query)) {
    $stmt->execute();
} else {
  echo("Not working");
}

?>
