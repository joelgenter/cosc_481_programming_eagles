<?php
require 'db_connection.php';        //$conn (mysqli connection) is now available

$query = "SELECT username, type, email FROM ProteinSim.Users";

$users = [];

if ($stmt = $conn->prepare($query)) {
    $stmt->execute();
    $stmt->bind_result($username, $type, $email);

    while ($stmt->fetch()) {
        $user = [
            "username" => $username,
            "type" => $type,
			"email" => $email
        ];
        array_push($users, $user);
    }

    $stmt->close();
}

echo json_encode($users);


// $stmt->bind_result($username, $fName, $lName, $email, $type);                //this is here as a reference of what order data is in the db
