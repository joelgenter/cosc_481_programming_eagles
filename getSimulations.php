<?php
	require 'db_connection.php';
    $aResult = array();
	$arrayOfData = array();
	
    $query = "SELECT Simulations.simulationName, Simulations.mutations, Simulations.startTime, Simulations.endTime, 
					 Simulations.username, Simulations.queuePosition, Simulations.id, Simulations.duration,Simulations.description  FROM Simulations ORDER BY queuePosition ASC";
	if ($stmt = $conn->prepare($query)) {
		$stmt->execute();
		$stmt->bind_result($simulationName, $mutations, $startTime, $endTime, $username, $queuePosition, $id, $duration, $description);
		while($stmt->fetch()){
			$data = array($simulationName,$mutations, $startTime, $endTime, $username, $queuePosition, $id, $duration, $description);
			array_push($arrayOfData,$data);
		}
	}
	$aResult['results'] = $arrayOfData;
    echo json_encode($aResult);
?>