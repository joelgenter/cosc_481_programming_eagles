<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Queue</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src ="queueFunctions.js"></script>
	<link rel="stylesheet" href="custom.css">
</head>
<body id>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display  -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
	        <span class="sr-only">Toggle navigation</span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </button>
			</div>
			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li><a href="index.php">Home</a></li>
					<li><a href="simulation.html">Simulation</a></li>
					<li class="active"><a href="queue.php">Queue<span class="sr-only">(current)</span></a></li>
					<li><a href="results.html">Results</a></li>
				</ul>
			</div>
			<!-- /.navbar-collapse -->
		</div>
		<!-- /.container-fluid -->
	</nav>
	<style>
		.form-control-inline {
			min-width: 0;
			width: auto;
			display: inline;
		}
		.thumbnail {
			padding: 30px;
		}
		.container-body {
			padding: 10%;
		}
	</style>
	<!-- Page Content -->
	<div id="contents" class="container-fluid container-body" style="padding-top: 20px;">

		<div class="row">
				<div class="col-lg-12">
					<div class="title">
						<h2>Simulation Queue</h2>
					</div>
				</div>
		</div>
		<div id ="simulationsList" class='panel panel-default'>
	<!-- Default panel contents -->
			<div  class='panel-heading'>
				<div class = 'row'>
					<div class = 'col-lg-2'>
						<h4>Simulation Name</h4>
					</div>
					<div class = 'col-lg-2'>
						<h4>Mutations</h4>
					</div>
					<div class = 'col-lg-2'>
						<h4>Requested By</h4>
					</div>
					<div class = 'col-lg-2'>
						<h4>Simulation Started</h4>
					</div>
					<div class = 'col-lg-2'>
						<h4>Esitmated End Time</h4>
					</div>
					<div class = 'col-lg-2 text-center'>
						<h4>Admin tools</h4>
					</div>
				</div>
			</div>


<?php
	/*
	require 'db_connection.php';

	$query = "SELECT Simulations.simulationName, Simulations.startTime, Simulations.endTime, Simulations.username, Simulations.mutations FROM Simulations";
	  if ($stmt = $conn->prepare($query)) {
      $stmt->execute();
	  $stmt->bind_result($simulationName, $startTime, $endTime, $username, $mutations);
	  echo("<!-- List group -->
			<ul class='list-group'>");
      while ($stmt->fetch()) {
          echo("
		<div class='list-group-item list-group-item-action'>
			<div class = 'row'>
				<div class = 'col-lg-2'>"
					.$simulationName.
				"</div>
				<div class = 'col-lg-2'>"
					.$mutations.
				"</div>
				<div class = 'col-lg-2'>"
					.$username.
				"</div>
				<div class = 'col-lg-2'>"
					.$startTime.
				"</div>
				<div class = 'col-lg-2'>"
					.$endTime.
				"</div>
				<div class = 'col-lg-2 text-center'>
					<button type='button' class='disabled btn btn-default btn-xs'>
						<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>
					</button>
					<button type='button' class='disabled btn btn-default btn-xs'>
						<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>
					</button>
					<button type='button' class='disabled btn btn-default btn-xs'>
						<span class='glyphicon glyphicon-trash' aria-hidden='true'></span>
					</button>
				</div>
			</div>
		</div>
");

      }
	  echo "</ul>";
      $stmt->close();
  }
	*/
?>
<script>
	generateSimulationsList();
</script>
</div>
<!--Something is wrong with the XAMPP installation :-(-->


	</div>
	<div class="container-fluid footer">
		<div class="row">
			<div class="col-lg-12 text-center">
				<p>Copyright &copy; Programming Eagles 2017
				</p>
			</div>
		</div>
</div>
</body>
</html>
