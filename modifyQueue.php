<?php
	include_once 'gpConfig.php';
	include_once 'User.php';
	require 'db_connection.php';
	//makes sure l33t h4x0rs can't edit queue too easily
	$cookie_name = "oauth_uid";
	$oauth_uid = $_COOKIE[$cookie_name];
	$user = new User();
	$status = $user->getStatus($oauth_uid);
	$status = $status['type'];
	//echo($status);
	if ($status == "admin"){
		//do nothing
	}
	else {
		header("Location: index.php");
		exit();
	}
	
    if( !isset($_POST['functionName']) ) { echo 'No function name!'; }
    if( !isset($_POST['arguments']) ) { echo 'No function arguments!'; }

    switch($_POST['functionName']) {
        case 'increment':
			if( count($_POST['arguments']) < 2 ) {
                echo 'Error in arguments!';
            }
			/*else if(floatval($_POST['arguments'][0]==0)){
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
			
			}*/
            else {
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
			$query = "DELETE FROM Simulations WHERE Simulations.queuePosition = ".$_POST['arguments'][0];
			if ($conn->query($query) === TRUE) {
				echo "Record deleted successfully";
			} else {
				echo "Error deleting record: " . $conn->error;
			}
			$query = " UPDATE Simulations SET Simulations.queuePosition = Simulations.queuePosition-1 WHERE Simulations.queuePosition > ".$_POST['arguments'][0] ;
			if ($conn->query($query) === TRUE) {
				echo "Record updated successfully";
			} else {
				echo "Error updating record: " . $conn->error;
			}
			break;
        default:
            echo 'Not found function '.$_POST['functionName'].'!';
            break;
    }

 ?>