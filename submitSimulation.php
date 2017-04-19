<?php
require 'db_connection.php';        //$conn (mysqli connection) is now available

//File Upload
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["pdbFile"]["name"]);
$uploadOk = 1;
$fileType = pathinfo($target_file,PATHINFO_EXTENSION);

// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}
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
$pdbFileName = filter_var ($_POST["pdbFileName"], FILTER_SANITIZE_STRING);
$pdbFile = $target_file;
$mutationList = filter_var ($_POST["mutationList"], FILTER_SANITIZE_STRING);
$username = filter_var ($_POST["username"], FILTER_SANITIZE_STRING);
$simulationName = filter_var ($_POST["simulationName"], FILTER_SANITIZE_STRING);
$description = filter_var ($_POST["description"], FILTER_SANITIZE_STRING);
$duration = filter_var ($_POST["duration"], FILTER_SANITIZE_STRING);
$temperature =filter_var ($_POST["temperature"], FILTER_SANITIZE_STRING);
//Get Current Queue Position
$currentQueue = 1000;
$query = "SELECT max(queuePosition) FROM ProteinSim.Simulations;";
if ($stmt = $conn->prepare($query)) {
    $stmt->execute();
    $stmt->bind_result($position);
    $stmt->fetch();
    $currentQueue = $position + 1;
    $stmt->close();
}

//Generate queries
$query="";
$simulationList = explode(";", $mutationList);

foreach ($simulationList as $mutation){
  $query .= "INSERT INTO ProteinSim.Simulations (mutations, pdbFileName, pdbFile, username, simulationName, description, duration, temperature, queuePosition) VALUES (\"".$mutation."\",\"".$pdbFileName."\",\"".$pdbFile."\",\"".$username."\",\"".$simulationName."\",\"".$description."\",\"".$duration."\",\"".$temperature."\",\"".$currentQueue."\");";
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
// header("Location: queue.php");
// die();
