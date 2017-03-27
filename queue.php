<?php
  require 'db_connection.php';        //$conn (mysqli connection) is now available

  $query = "SELECT * FROM ProteinSim.Simulations";


  if ($stmt = $con->prepare($query)) {
      $stmt->execute();
      $stmt->bind_result($field1, $field2);
      while ($stmt->fetch()) {
          //printf("%s, %s\n", $field1, $field2);
      }
      $stmt->close();
  }
