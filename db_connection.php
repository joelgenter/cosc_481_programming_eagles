<?php
$databaseCredentials = parse_ini_file('./.ini');

$conn = new mysqli(
    $databaseCredentials['host'],
    $databaseCredentials['user'],
    $databaseCredentials['password'], 
    $databaseCredentials['dbname'],
    $databaseCredentials['port'],
    $databaseCredentials['socket']
) or die ('Could not connect to the database server' . mysqli_connect_error());