<?php
	require 'db_connection.php';
  
	$arrayOfData = array();
	$id = $_POST['id'];

    $query = "SELECT Simulations.simulationName, Simulations.mutations, Simulations.startTime, 
			  Simulations.endTime, Simulations.username, Simulations.queuePosition, Simulations.id, 
			  Simulations.duration, Simulations.description FROM Simulations WHERE Simulations.id = '".$id."'";
	if ($stmt = $conn->prepare($query)) {
		$stmt->execute();
		$stmt->bind_result($simulationName,$mutations, $startTime, $endTime, $username, $queuePosition, $results, $duration, $desciption);
		while($stmt->fetch()){
			$data = array($simulationName,$mutations, $startTime, $endTime, $username, $queuePosition, $results, $duration, $desciption);
			array_push($arrayOfData,$data);
		}
	}
	
    echo json_encode($arrayOfData);
?>