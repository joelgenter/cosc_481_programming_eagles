<?php
$host="127.0.0.1";
$port=3306;
$socket="";
$user="proteinSim";
$password="Gromacs#2017";
$dbname="ProteinSim";

$con = new mysqli($host, $user, $password, $dbname, $port, $socket)
	or die ('Could not connect to the database server' . mysqli_connect_error());


  $query = "SELECT * FROM Users";


  if ($stmt = $con->prepare($query)) {
      $stmt->execute();
      $stmt->bind_result($field1, $field2);
      while ($stmt->fetch()) {
          /printf("%s, %s\n", $field1, $field2);
      }
      $stmt->close();
  }


$con->close();
