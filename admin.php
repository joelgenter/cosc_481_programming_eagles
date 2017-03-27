<?php
require 'db_connection.php';        //$conn (mysqli connection) is now available

$query = "SELECT * FROM ProteinSim.Users";


if ($stmt = $con->prepare($query)) {
    $stmt->execute();
    $stmt->bind_result($username, $fName, $lName, $email, $type);
    while ($stmt->fetch()) {
        printf("%s, %s\n", $username, $fName, $lName, $email, $type);
    }
    $stmt->close();
}
