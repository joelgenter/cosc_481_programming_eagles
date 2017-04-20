<?php
	require 'db_connection.php';
  
	$arrayOfData = array();
	$id = $_POST['id'];
	//echo("\'".$results."\'");
    $query = "SELECT Simulations.simulationName, Simulations.mutations, Simulations.startTime, 
			  Simulations.endTime, Simulations.username, Simulations.queuePosition, Simulations.id, 
			  Simulations.duration FROM Simulations WHERE Simulations.id = '".$id."'";
	if ($stmt = $conn->prepare($query)) {
		$stmt->execute();
		$stmt->bind_result($simulationName,$mutations, $startTime, $endTime, $username, $queuePosition, $results, $duration);
		while($stmt->fetch()){
			$data = array($simulationName,$mutations, $startTime, $endTime, $username, $queuePosition, $results, $duration);
			array_push($arrayOfData,$data);
		}
	}
	
    echo json_encode($arrayOfData);
?>