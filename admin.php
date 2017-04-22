<?php
include_once 'gpConfig.php';
include_once 'User.php';

$cookie_name = "oauth_uid";
$oauth_uid = $_COOKIE[$cookie_name];
$user = new User();
$status = $user->getStatus($oauth_uid);
$status = $status['type'];

if ($status == "admin"){
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

	//Turn off caching for this page
	header("Cache-control: no-store, no-cache, must-revalidate");
	header("Expires: Mon, 26 Jun 1997 05:00:00 GMT");
	header("Pragma: no-cache");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
?>


<!DOCTYPE HTML>
<html>
<head>
<!-- Set Cache-control to not cache this page -->
<meta http-equiv="Expires" CONTENT="0">
<meta http-equiv="Cache-Control" CONTENT="no-cache">
<meta http-equiv="Pragma" CONTENT="no-cache">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Admin</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="admin.js"></script>
<link rel="stylesheet" href="custom.css">
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
	}
	.fa {
		font-size: 20px;
		cursor: pointer;
	}
	list-group-item .row div {
		display: flex;
		align-items: center;
	}
</style>
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
					<li><a href="results.php">Results</a></li>
					<li class="active"><a href="admin.php">Admin<span class="sr-only">(current)</span></a></li>
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
						<h2>Current Users</h2>
					</div>
				</div>
		</div>
		<div class="panel panel-default">
			<!-- Default panel contents -->
			<div class="panel-heading">
				<div class = "row">
					<div class = "col-lg-2 col-sm-4"><h4>Username</h4></div>
					<div class = "col-lg-2 col-sm-4"><h4>Name</h4></div>
					<div class = "col-lg-2 col-sm-4"><h4>Email</h4></div>
					<div class = "col-lg-2 col-sm-4"><h4>Admin Toggle</h4></div>
					<div class = "col-lg-2 col-sm-4"><h4>Remove User</h4></div>
				</div>
			</div>

			<!-- <div class="input-group col-lg-12">
				<input id="pdbSearch" type="text" class="form-control" placeholder="New emich user (Ex. jsmith1)">
				<span class="input-group-btn">
					<button id="searchButton" class="btn btn-default" type="button">Add</button>
				</span>
			</div> -->

			<!-- List group -->
			<ul id="usersList" class="list-group">

			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12 text-center">
				<p>Copyright &copy; Programming Eagles 2017
				</p>
			</div>
		</div>
</div>

<script>
generateUsers(<?php echo("'".$email['email']."'"); ?>);

</script>
</body>
</html>
