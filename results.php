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
<title>Results List</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="resultsList.js"></script>
<link rel="stylesheet" href="custom.css">
</head>
<body>

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
					<li><a href="queue.php">Queue</a></li>
					<li class="active"><a href="results.php">Results<span class="sr-only">(current)</span></a></li>
					<li><?php echo $admin; ?></li>
	      </ul>
				<ul class="nav navbar-nav navbar-right">
					<li><div class="sign-in" align="right";><?php echo $output; ?></div></li>
				</ul>
	    </div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
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
.container-body{
  padding: 10%;
}</style>
	<!-- Page Content -->
	<div class="container-fluid container-body"style="padding-top: 20px;">
		<div class="row">
				<div class="col-lg-12">
					<div class="title">
						<h2>Completed Simulations</h2>

					</div>
				</div>
		</div>
		<div id="resultsList" class="panel panel-default">
			<!-- Default panel contents -->
			<div class="panel-heading">
				<div class = "row">
					<div class = "col-sm-6 col-sm-6 col-xs-12">
						<h4>Simulation Name</h4>
					</div>
					<div class = "col-lg-4 col-sm-4 col-xs-12">
						<h4>Requested By</h4>
					</div>
					<div class = "col-lg-2 col-sm-2 col-xs-12">
						<h4>Date Completed</h4>
					</div>
				</div>
			</div>
			<!-- List group
			<ul class="list-group">
				<a href = "sampleResult.html" class="list-group-item list-group-item-action">
					<div class = "row">
						<div class = "col-lg-6 col-sm-6 col-xs-12">
							Test Simulation1
						</div>
						<div class = "col-lg-4 col-sm-4 col-xs-12">
							Jeremy Ginnard
						</div>
						<div class = "col-lg-2 col-sm-2 col-xs-12">
							02-13-2017,00:52
						</div>
					</div>
				</a>
				<a href = "sampleResult.html" class="list-group-item list-group-item-action">
					<div class = "row">
						<div class = "col-lg-6 col-sm-6 col-xs-12">
							UHFRC-2 Histone Binding Levels
						</div>
						<div class = "col-lg-4 col-sm-4 col-xs-12">
							Dr. Albaugh
						</div>
						<div class = "col-lg-2 col-sm-2 col-xs-12">
							02-14-2017,15:22
						</div>
					</div>
				</a>
				<a href = "sampleResult.html" class="list-group-item list-group-item-action">
					<div class = "row">
						<div class = "col-lg-6 col-sm-6 col-xs-12">
							UHFRC-1 Histone Binding Levels
						</div>
						<div class = "col-lg-4 col-sm-4 col-xs-12">
							Dr. Albaugh
						</div>
						<div class = "col-lg-2 col-sm-2 col-xs-12">
							02-14-2017,17:16
						</div>
					</div>
				</a>
				<a href = "sampleResult.html" class="list-group-item list-group-item-action">
					<div class = "row">
						<div class = "col-lg-6 col-sm-6 col-xs-12">
							Test Simulation Please Ignore
						</div>
						<div class = "col-lg-4 col-sm-4 col-xs-12">
							Isaac Kane
						</div>
						<div class = "col-lg-2 col-sm-2 col-xs-12">
							02-16-2017,02:00
						</div>
					</div>
				</a>
				<a href = "sampleResult.html" class="list-group-item list-group-item-action">
					<div class = "row">
						<div class = "col-lg-6 col-sm-6 col-xs-12">
							A Simulation Test Run
						</div>
						<div class = "col-lg-4 col-sm-4 col-xs-12">
							Daniel Clarke
						</div>
						<div class = "col-lg-2 col-sm-2 col-xs-12">
							02-18-2017,22:25
						</div>
					</div>
				</a>
			</ul> -->
		</div>
	</div>
	<script>
		generateResultsList()
	</script>
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12 text-center">
				<p>Copyright &copy; Programming Eagles 2017
				</p>
			</div>
		</div>
</div>
</body>
</html>
