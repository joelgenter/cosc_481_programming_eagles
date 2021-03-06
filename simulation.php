<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
	<title>Simulation</title>
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
	<script src="bootstrap-dialog.min.js"></script>
	<script src="simulation.js"></script>
	<link rel="stylesheet" href="custom.css">
</head>

<body>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
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
					<li class="active"><a href="simulation.php">Simulation<span class="sr-only">(current)</span></a></li>
					<li><a href="queue.php">Queue</a></li>
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
	<!-- Page Content -->
	<div class="container-fluid container-body" style="padding-top: 20px;">
		<div class="title">
			<h2>Simulation Setup</h2>
			<legend>Instructions</legend>
			<p><strong>Step 1:</strong> Download PDB file from the <a target="_blank" href="http://www.rcsb.org/pdb/home/home.do">Protein Data Bank</a>.<br />
			<strong>Step 2:</strong> Fix any breaks in the PDB file if necessary using a program such as <a target="_blank" href="https://www.pymol.org/"> PyMol</a>.<br />
			<strong>Step 3:</strong> Upload PDB file to <a target="_blank" href="http://vienna-ptm.univie.ac.at/">Vienna-PTM</a>. Select "ffG54a7" as the force field. Submit the form and follow the instructions to make your mutations. Download the modified PDB file.<br />
			<strong>Step 4:</strong> Upload the modified PDB file to this page and complete the form to run your simulation.
			</p>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<form action ="submitSimulation.php" method="POST" enctype="multipart/form-data" onsubmit="sumbitSimulation();">
					<fieldset class="form-group">
						<legend>PDB File</legend>
						<!-- <p>The PDB file must not have any breaks or the simulation will not work.
							<br>You may need to build a PDB file using PyMol.</p> -->
									<!--Upload Option-->
									<label for="pdbUploadOption">Upload Custom PDB File</label>
							<div class="form-group">
								<input type="file" class="form-control-file" id="pdbUpload" name="pdbFile" aria-describedby="fileHelp" required>
							</div>

					</fieldset>
					<fieldset class="form-group">
						<legend>Simulation Parameters</legend>

							<!--List Option-->
									<label for="mutantList">List of Mutants (Seperate Simulations)</label>
							<div id="mutantList">
								<div class="form-group">
									<input type="text" class="form-control" id="mutantListField1" aria-describedby="mutantHelp" placeholder="mutant 1" required>
									<small id="mutantHelp" class="form-text text-muted">Example "Y213A, Y216A, F144A"</small>
								</div>
							</div>
							<button id="addMutantButton" class="btn btn-default" type="button" onclick="addMutantField()">+ Add Mutant</button>
							<br><br>
							<div class="form-group">
						    <label for="forceField">Force Field</label>
						    <select class="form-control" id="forceField" name="forceField">
						      <option value="1">GROMOS96 54a7 (custom)</option>
						      <option value="2">AMBER03</option>
						      <option value="3">AMBER94</option>
						      <option value="4">AMBER96</option>
						      <option value="5">AMBER99</option>
									<option value="6">AMBER99SB</option>
									<option value="7">AMBER99SB-ILDN</option>
									<option value="8">AMBERGS</option>
									<option value="9">CHARMM27 all-atom</option>
									<option value="10">GROMOS96 43a1</option>
									<option value="11">GROMOS96 43a2</option>
									<option value="12">GROMOS96 45a3</option>
									<option value="13">GROMOS96 53a5</option>
									<option value="14">GROMOS96 53a6</option>
									<option value="15">GROMOS96 54a7</option>
									<option value="16">OPLS-AA/L all-atom</option>
						    </select>
						  </div>
							<div class="form-group">
								<label for="duration">Duration (ns)</label>
								<input type="number" class="form-control" id="duration" name="duration" value="1" min="1" max="40">
							</div>
							<div class="form-group">
								<label for="temperature">Temperature (Celsius)</label>
								<input type="number" class="form-control" id="temperature" name="temperature" value="30" min="0">
							</div>
							<div class="form-group">
						    <label for="frames">Dynamic Visualization Frames</label>
						    <select class="form-control" id="frames" name="frames">
									<option value="0">Off</option>
						      <option value="100">100</option>
						      <option value="200">200</option>
						      <option value="300">300 (recommended)</option>
						      <option value="400">400</option>
						      <option value="500">500</option>
									<option value="600">600</option>
						    </select>
						  </div>
					</fieldset>
					<fieldset class="form-group">
						<legend>Additional Information</legend>
						<div class="form-group">
							<label for="simulationName">Simulation Name</label>
							<input type="text" class="form-control" id="simulationName" name="simulationName" required>
						</div>
						<div class="form-group">
							<label for="exampleTextarea">Description</label>
							<textarea class="form-control" id="description" name="description" rows="3"></textarea>
						</div>
					</fieldset>
					<input type="hidden"id="username" name="oauth_uid" value=<?php echo "\"".$oauth_uid."\""?>></input>
					<input type="hidden" id="mutationList" name="mutationList"></input>
					<input type="hidden" id="pdbFileName" name="pdbFileName"></input>
					<button type="submit" class="btn btn-primary">Submit</button>
				</form>
			</div>
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
</body>
</html>
