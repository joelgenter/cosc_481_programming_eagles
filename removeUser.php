<?php
// PHP to remove user from the Users table of ProteinSim
require 'db_connection.php';        //$conn (mysqli connection) is now available

$username = $_POST['username'];

$query = "DELETE FROM ProteinSim.Users WHERE username = '$username'";
if ($stmt = $conn->prepare($query)) {
    $stmt->execute();
} else {
  echo("Not working");
}

?>
