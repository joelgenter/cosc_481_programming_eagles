<?php
	require 'db_connection.php';
  
    if( !isset($_POST['functionName']) ) { echo 'No function name!'; }
    if( !isset($_POST['arguments']) ) { echo 'No function arguments!'; }

    switch($_POST['functionName']) {
        case 'increment':
			if( count($_POST['arguments']) < 2 ) {
                echo 'Error in arguments!';
            }
			else if(floatval($_POST['arguments'][0]==1)){
				$query = "UPDATE Simulations 
						  SET Simulations.queuePosition = CASE
							WHEN Simulations.queuePosition > 1 THEN Simulations.queuePosition-1
							WHEN Simulations.queuePosition = 1 THEN -1
							ELSE Simulations.queuePosition
						  END;";
					if ($conn->query($query) === TRUE) {
						echo "Record updated successfully";
					} else {
						echo "Error updating record: " . $conn->error;
					}
			}
            else {
				print_r($_POST['arguments']);
				$query = "UPDATE Simulations 
						  SET Simulations.queuePosition = CASE
							WHEN Simulations.queuePosition = ".$_POST['arguments'][0]." THEN ".$_POST['arguments'][1]." 
							WHEN Simulations.queuePosition = ".$_POST['arguments'][1]." THEN ".$_POST['arguments'][0]."
							ELSE Simulations.queuePosition
						  END
						  WHERE
							Simulations.queuePosition IN (".$_POST['arguments'][0].",".$_POST['arguments'][1].");";
					if ($conn->query($query) === TRUE) {
						echo "Record updated successfully";
					} else {
						echo "Error updating record: " . $conn->error;
					}
            }
            break;
		case 'delete':
			
			break;
        default:
            echo 'Not found function '.$_POST['functionName'].'!';
            break;
    }

 ?>