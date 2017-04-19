<?php
	require 'db_connection.php';
    $aResult = array();
	$arrayOfData = array();
	
	$results = $_POST['results'];
	echo("\'".$results."\'");
    $query = "SELECT Simulations.simulationName, Simulations.mutations, Simulations.startTime, 
			  Simulations.endTime, Simulations.username, Simulations.queuePosition, Simulations.results, 
			  Simulations.duration FROM Simulations WHERE Simulations.results = '".$results."'";
	if ($stmt = $conn->prepare($query)) {
		$stmt->execute();
		$stmt->bind_result($simulationName,$mutations, $startTime, $endTime, $username, $queuePosition, $results, $duration);
		while($stmt->fetch()){
			$data = array($simulationName,$mutations, $startTime, $endTime, $username, $queuePosition, $results, $duration);
			array_push($arrayOfData,$data);
		}
	}
	$aResult['info'] = $arrayOfData;
    echo json_encode($aResult);
?>