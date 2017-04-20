<?php
/*
The gromacs log file outputs the status of the simulation in a group of data
points. The percentage completion of the simulation seems to be labeled as 'Time'.
The challenge is that the value is on a separate line (below) from the label.
However, it appears that the right-most digit of the value aligns with the 'e'
in 'Time'. The following code reads the file from the end to the beginning. 
Lines will be read into a set of two variables ($top_line and bottom_line) one
at a time. $top_line will be searched for the string 'Time'. If it's found, 
the valule below 'Time' will be read and echoed.
*/

//Need to change this path to path of log file that gromacs will be producing

/*
em        between 0 - 1000ps, stops when minimized
nvt        100ps
npt        100ps
md_0_1        1000ps - 40000ps, (whatever the user chooses
fec        1000ps
*/
$fileName = $_POST['results'];

if( ! file_exists($fileName)){
	die();
}
$data = []; //data points
	
$file = fopen($fileName, "r");
$top_line = "";
$bottom_line = "";
$count =0;
for ($x_position = 0, $new_line = ''; fseek($file, $x_position, SEEK_END) !== -1; $x_position--) {
		$char = fgetc($file);
		if ($char === "\n") {
			$bottom_line = $new_line;
				//echo "|Bottom: " . $bottom_line . "|";
				$keyword_position = strpos($bottom_line, '+/-');
			if ($keyword_position != FALSE) {
				//read from $bottom_line backwards 
				//beginning where 'e' is positioned
				$keyword_position -= 2; //the position of 'e'
				$energy = '';
				$current_char = substr($bottom_line, $keyword_position, 1);
				while (preg_match("/[0-9.]|\-|\./", $current_char)) {
					$energy = $current_char . $energy;
					$keyword_position--;
					$current_char = substr($bottom_line, $keyword_position, 1);
				}
				$count = $count +1;
				//echo '-->'.$energy.'<--';
				//$nextResult = [$energy];
				if($count>1)
				array_unshift($data,floatval($energy));
				if($count > 20){
					break;      //Breaks for loop. We have our precentage. No need to continue.
				}
			}
				$new_line = '';
		} else {
			$new_line = $char . $new_line;
		}
		
}

fclose($file);


echo json_encode($data);