<!DOCTYPE html>
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
$results = $_GET['results'];
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Result Page</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.js"></script>
<script src="resultsPage.js"></script>
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
	<!-- Page Content -->
	<div class="container-fluid container-body"style="padding-top: 20px;">
		<div class="row">
				<div class="col-lg-12">
					<div class="title">
						<h2>Results</h2>
					</div>
				</div>
		</div>
		<div>
			<legend>
				<h3 id = 'title'>Test Simulation1</h3>
				<h4 id = 'user'>Submitted by: Jeremy Ginnard</h4>
			</legend>
		<div>
		<div>
			<canvas id="myChart" width="400" height="400"></canvas>
		</div>
		<p>
			<legend>Description</legend>
			<span id='description'>This was a test of the simulation. The graph represents the difference in free energy
			from the base protein to the mutations requested. The 23 bars show the positive or
			negative difference between the new mutated protein an the original protein.</span>
		</p>
			</br>
		</div>
		<div>
			<!--<legend>Data</legend>-->
		
		</div>
		</br>
		</br>
	</div>
</div>
<script>
   generateResults(<?php echo "'".$results."\'" ?>)
</script>
</div>
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
