<?php
require 'db_connection.php';        //$conn (mysqli connection) is now available

//Form data
$pdbFileName = $_POST["pdbFileName"];
$pdbFile = $_POST["pdbFile"];
$mutationList = $_POST["mutationList"];
$username = $_POST["username"];
$simulationName = $_POST["simulationName"];
$description = $_POST["description"];

//Generate queries
$query;
$simulationList = explode(";", $mutationList);

foreach ($simulationList as $mutation){
  $query .= "INSERT INTO ProteinSim.Simulations (mutations, pdbFileName, pdbFile, username, simulationName, description) VALUES (\"".$mutation."\",\"".$pdbFileName."\",\"".$pdbFile."\",\"".$username."\",\"".$simulationName."\",\"".$description."\");";
}


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
//   if ($stmt = $con->prepare($query)) {
//       $stmt->execute();
//       $stmt->close();
//       echo "Query Sent";
//   }
//
// $con->close();

if (mysqli_multi_query($conn, $query)) {
    echo "New records created successfully";
} else {
    echo "Error: " . $query . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
//echo "submitted query: ".$query;
header("Location: queue.php");
die();
