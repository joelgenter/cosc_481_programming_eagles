<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
require 'db_connection.php';        //$conn (mysqli connection) is now available
include_once 'User.php';
$num = 0;
$pdbFileName = filter_var ($_POST["pdbFileName"], FILTER_SANITIZE_STRING);
//File Upload
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["pdbFile"]["name"]);
$uploadOk = 1;
$fileType = pathinfo($target_file,PATHINFO_EXTENSION);

// Check if file already exists
while (file_exists($target_file)) {
    $num++;
    echo "Sorry, file already exists.";
    $target_file = $target_dir . basename($_FILES["pdbFile"]["name"])."(".$num.")"; //Rename the file
    $pdbFileName = basename($_FILES["pdbFile"]["name"])."(".$num.")";
    //$uploadOk = 1;
}
//Replace all spaces with "_"
$target_file = str_replace(" ", "_", $target_file);
$pdbFileName = str_replace(" ", "_", $pdbFileName);
// Check file size
if ($_FILES["pdbFile"]["size"] > 50000000) { //50MB limit
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($fileType != "pdb") {
    echo "Sorry, only PDB files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["pdbFile"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["pdbFile"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

//Form data

$mutationList = filter_var ($_POST["mutationList"], FILTER_SANITIZE_STRING);
$oAuthId = filter_var ($_POST["oauth_uid"], FILTER_SANITIZE_STRING);
$user = new User();
$username = $user->getUsername($oAuthId);
$username = $username['username'];
echo "username = ".$username;
$simulationName = filter_var ($_POST["simulationName"], FILTER_SANITIZE_STRING);
$description = filter_var ($_POST["description"], FILTER_SANITIZE_STRING);
$duration = filter_var ($_POST["duration"], FILTER_SANITIZE_STRING);
$temperature = filter_var ($_POST["temperature"], FILTER_SANITIZE_STRING);
$forceField = filter_var ($_POST["forceField"], FILTER_SANITIZE_STRING);
$frames = filter_var ($_POST["frames"], FILTER_SANITIZE_STRING);

//Get Current Queue Position
$currentQueue = 1000;
$query = "SELECT max(queuePosition) FROM ProteinSim.Simulations;";
if ($stmt = $conn->prepare($query)) {
    $stmt->execute();
    $stmt->bind_result($position);
    $stmt->fetch();
    $currentQueue = ($position == -1)? 1 : $position + 1;
    $stmt->close();
}

//Generate queries
$query="";
$simulationList = explode(";", $mutationList);

foreach ($simulationList as $mutation){
  echo "queue position: ".$currentQueue;
  echo "forcefield: ".$forceField;
  $query .= "INSERT INTO ProteinSim.Simulations (mutations, pdbFileName, username, simulationName, description, duration, temperature, queuePosition, forceField, frames) VALUES (\"".$mutation."\",\"".$pdbFileName."\",\"".$username."\",\"".$simulationName."\",\"".$description."\",\"".$duration."\",\"".$temperature."\",\"".$currentQueue."\",\"".$forceField."\",\"".$frames."\");";
  $currentQueue += 1;
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

//Run Simulation
// $log = shell_exec("/home/gromacs/simulations/simulationManager.sh");
// echo $log;
shell_exec("/home/gromacs/simulations/simulationManager.sh > /dev/null &");
header("Location: queue.php");
die();
