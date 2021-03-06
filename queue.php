<?php
include_once 'gpConfig.php';
include_once 'User.php';

$cookie_name = "oauth_uid";
$oauth_uid = $_COOKIE[$cookie_name];
$user = new User();
$status = $user->getStatus($oauth_uid);
$status = $status['type'];

if ($status == "admin" || $status == "standard"){
	//do nothing
}
else {
	header("Location: index.php");
	exit();
}

//produce admin tab if admin
$admin = "";
if ($status == "admin")
{
	$admin = '<a href="admin.php">Admin</a>';
}

//get user email
$email = $user->getEmail($oauth_uid);

//Render user email and logot text
	if(!empty($email))
	{
			$output = 'Logged in as: ' . $email['email'];
			$output .= '<br/><a href="logout.php">Logout of EMU ProteinSim</a>';
	}
	else
	{
			$output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
	}
?>

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
					<li><a href="simulation.php">Simulation</a></li>
					<li class="active"><a href="queue.php">Queue<span class="sr-only">(current)</span></a></li>
					<li><a href="results.php">Results</a></li>
					<li><?php echo $admin; ?></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li><div class="sign-in" align="right";><?php echo $output; ?></div></li>
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
		.progress span {
			position: absolute;
			display: block;
			width: 100%;
			color: black;
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
					<div class = 'col-lg-3'>
						<h4>Simulation Name</h4>
					</div>
					<div class = 'col-lg-2'>
						<h4>Mutations</h4>
					</div>
					<div class = 'col-lg-2'>
						<h4>Requested By</h4>
					</div>
					<div class = 'col-lg-3'>
						<h4>Simulation Started</h4>
					</div>
					<div class = 'col-lg-2 text-center'>
						<h4>Admin tools</h4>
					</div>
				</div>
			</div>
			<div class="modal fade" id="alertModal" role="dialog">
				<div class="modal-dialog">
    
				<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header bg-primary">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 id='modalTitle' class="modal-title">Confirm Delete</h4>
						</div>
						<div id='modalText' class="modal-body">
							<p>Are you sure you want to delete this simulation?</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="button" id = "confirmDelete" class="btn btn-default hide" data-dismiss="modal">Delete</button>
							<button type="button" id = "confirmMove" class="btn btn-default hide" data-dismiss="modal">Confirm</button>
						</div>
					</div>
      
				</div>
			</div>
<script>
	$(function(){
		$('#confirmDelete').click(function(){
			deleteSim(this.name[this.name.length-1],<?php echo "\"".$status."\""; ?>)
		})
	})
	$(function(){
		$('#confirmMove').click(function(){
			decrementSim(0,<?php echo "\"".$status."\""; ?>)
		})
	})
	generateSimulationsList(<?php echo "\"".$status."\""; ?>);
</script>
</div>
<!--Something is wrong with the XAMPP installation :-(-->


	</div>
	<!--<div class="container-fluid footer">
		<div class="row">
			<div class="col-lg-12 text-center">
				<p>Copyright &copy; Programming Eagles 2017
				</p>
			</div>
		</div>
	</div>-->
</body>
</html>
