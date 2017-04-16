<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
//Include GP config file && User class
include_once 'gpConfig.php';
include_once 'User.php';

if(isset($_GET['code'])){
	$gClient->authenticate($_GET['code']);
	$_SESSION['token'] = $gClient->getAccessToken();
	header('Location: ' . filter_var($redirectURL, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['token'])) {
	$gClient->setAccessToken($_SESSION['token']);
}

if ($gClient->getAccessToken()) {
	//Get user profile data from google
	$gpUserProfile = $google_oauthV2->userinfo->get();

	//Initialize User class
	$user = new User();

	//gets username
	$email = $gpUserProfile['email'];
	$index = strpos($email, '@');
	$username = substr($email, 0, $index);

	//set username
	//$user->setUsername($username);

	//Insert or update user data to the database
	$gpUserData = array(
	    'oauth_provider'=> 'google',
	    'oauth_uid'     => $gpUserProfile['id'],
			'username'			=> $username,
	    'first_name'    => $gpUserProfile['given_name'],
	    'last_name'     => $gpUserProfile['family_name'],
	    'email'         => $gpUserProfile['email']
	);

  $userData = $user->checkUser($gpUserData);

	//--------------------------------------------------------

	//checks the users permissions
	$status = $user->getStatus($username);
	$statusOutput = $status;
	echo $statusOutput['type'];
	echo "<br>Hello world</br>";
	//echo $user->getUsername();

	//--------------------------------------------------------


	//Storing user data into session
	$_SESSION['userData'] = $userData;

	//Render facebook profile data
    if(!empty($userData))
		{
        $output = '<br/>Logged in as: ' . $userData['email'];
        $output .= '<br/><a href="logout.php">Logout of EMU ProteinSim</a>';
    }
		else
		{
        $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
    }
}
else
{
	$authUrl = $gClient->createAuthUrl();
	$output = '<a href="'.filter_var($authUrl, FILTER_SANITIZE_URL).'"><img src="images/glogin3.PNG" alt=""/></a>';
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Home</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="custom.css">

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<!-- username cookie -->
<script src=usernameCookie.js></script>
<script>

var data = <?php echo json_encode($username, JSON_HEX_TAG); ?>;
var username = "" + data;

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function checkCookie() {
	var user = getCookie("username");
	if (user != "") {
			alert("Welcome again " + user);
	} else {
			user = prompt("Please enter your name:", "");
			if (user != "" && user != null) {
					setCookie("username", user, 365);
			}
	}
}

function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
					c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
					return c.substring(name.length, c.length);
			}
	}
	return "";
}

//creates a cookie with the username of the user
setCookie("username", username, 365);

// displays the username
checkCookie();

</script>

<style type="text/css">
	h1{font-family:Arial, Helvetica, sans-serif;color:#999999;}

	.sign-in {
		padding-top: 0em;
		margin-top: -1em;
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
					<li class="active"><a href="index.php">Home<span class="sr-only">(current)</span></a></li>
					<li><a href="simulation.html">Simulation</a></li>
					<li><a href="queue.php">Queue</a></li>
					<li><a href="results.html">Results</a></li>
					<li><a href="register.php">Register</a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li><div class="n/a" align="right";><?php echo $output; ?></div></li>
				</ul>
			</div>
			<!-- /.navbar-collapse -->
		</div>
		<!-- /.container-fluid -->
	</nav>
	<div class="container-fluid banner">
		<div class="row">
			<div class="col-lg-12">
				<h1>Protein Simulations</h1>
				<p>Helping to Cure Cancer At Eastern Michigan University</p>
			</div>
		</div>
	</div>

	<!-- Page Content -->
	<div class="container-body">
		<div class="row">
			<div class="col-lg-12">
				<div class="title">
					<h2>Protein Simulations</h2>
					<span class="byline">A Colloborative Project Between the EMU Computer Science and Chemistry Departments</span>
				</div>
				<h3>Purpose</h3>
				<p>The goal of this project is to aid Dr. Albaugh's research team by allowing
					them to compare the calculated free energy for various mutations. This
					allows the research team to narrow down possible mutations on which to
					continue their research.</p>
				<h3>About The Team</h3>
				<p>This system is developed by Jeremy Ginnard, Steven Sotok, Joel Genter,
					Issac Kane, and Daniel Clarke as part of our capstone project.</p>
				<h3>About This System</h3>
				<p>This system uses only open source software. The protein simulations are
				performed using GROMACS. Protein structures are obtained from the Protein Data Bank.</p>
			</div>
		</div>
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
