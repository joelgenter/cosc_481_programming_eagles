<?php
require 'db_connection.php';        //$conn (mysqli connection) is now available

//Form data
$pdbFileName = filter_var ($_POST["pdbFileName"], FILTER_SANITIZE_STRING);
$pdbFile;
if (pathinfo($pdbFileName)['extension'] == "pdb"){
  $pdbFile= $_POST["pdbFile"];
}
$mutationList = filter_var ($_POST["mutationList"], FILTER_SANITIZE_STRING);
$username = filter_var ($_POST["username"], FILTER_SANITIZE_STRING);
$simulationName = filter_var ($_POST["simulationName"], FILTER_SANITIZE_STRING);
$description = filter_var ($_POST["description"], FILTER_SANITIZE_STRING);
$duration = filter_var ($_POST["duration"], FILTER_SANITIZE_STRING);
$temperature =filter_var ($_POST["temperature"], FILTER_SANITIZE_STRING);

//Generate queries
$query;
$simulationList = explode(";", $mutationList);

foreach ($simulationList as $mutation){
  $query .= "INSERT INTO ProteinSim.Simulations (mutations, pdbFileName, pdbFile, username, simulationName, description, duration, temperature) VALUES (\"".$mutation."\",\"".$pdbFileName."\",\"".$pdbFile."\",\"".$username."\",\"".$simulationName."\",\"".$description."\",\"".$duration."\",\"".$temperature."\");";
}


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
