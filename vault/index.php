<?php
	session_start();
	include("../config.php");

	if(isset($_SESSION["download"])) {
		if (isset($_SESSION["multifile"]) && $_SESSION["multifile"] == 1) {
			header("Location: vault/?id=" . $hash);
		}
		else {
			session_destroy();
			header("Location: ../");
		}
	}
	else if(isset($_GET["id"])) {
		$hash = $_GET["id"];
		
		$dbQuery=$db->prepare("select * from files where hash=:hash");
		$dbParams = array('hash'=>$hash);
		$dbQuery->execute($dbParams);
		$fileCount = $dbQuery->rowCount();

		if($fileCount > 1) {
			$_SESSION["multifile"] = 1;
		}
		else {
			$_SESSION["multifile"] = 0;
		}
		
		$files = array();
		
		while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
		{
			$files[$dbRow["id"]] = $dbRow["filename"];
		}

		if(isset($_SESSION["user"])) { 
			$user = $_SESSION["user"];
		}
		else {
			header("Location: ../auth/?id=" . $hash);
		}
		
		$dbQuery=$db->prepare("select * from auth where hash=:hash");
		$dbParams = array('hash'=>$hash);
		$dbQuery->execute($dbParams);
		$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
		
		$auth = $dbRow["users"];
		
		$authenticatedUsers = explode(",", $auth);

		foreach ($authenticatedUsers as $authenticatedUser) {
			if($authenticatedUser == $user) {
				$_SESSION["auth"] = 1;
			}
		}

		if($_SESSION["auth"] != 1)
		{
			header("Location: ../auth/?error=You%20do%20not%20have%20permission%20to%20access%20this%20file&id=" . $hash);
		}
	}
	else {
		$error = "No file requested. Invalid URL.";
	}
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="pix/favicon.ico">

		<title>DK Secure | Vault</title>

		<!-- Bootstrap core CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css" integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous">

		<!-- Font Awesome CSS -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">

		<!-- Custom styles for this template -->
		<link href="css/pricing.css" rel="stylesheet">
	</head>

	<body>

		<div class="container" style="padding-top: 20px;">
		  <nav class="navbar navbar-expand-lg navbar-light bg-light">
			<a class="navbar-brand" href="../">DK Secure</a>
			<ul class="navbar-nav mr-auto">
			</ul>
			<span class="navbar-text navbar-text-nav">Logged in as <?php echo $user; ?> (<a href="logout.php">Log out</a>)</span>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>

			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<span class="navbar-text navbar-text-collapsed">Logged in as <?php echo $user; ?> (<a href="logout.php">Log out</a>)</span>
			</div>
		  </nav>
		  <br>

		<?php
			if (isset($error)) {
				echo "<h1 style='color:red'>".$error."</h1>";
			}
			else {
				
				foreach ($files as $fileid => $file) {
		?>
					<form method="post" action="../download.php">
						<div class="card">
							<div class="card-body">
								<h5 class="card-title"><?php echo $file; ?></h5>
								<i class="far fa-folder-open fa-5x"></i>
								
								<input type="hidden" name="user" value="<?php echo $user; ?>">
								<input type="hidden" name="fileid" value="<?php echo $fileid; ?>">
								<button style="float:right" type="submit" class="btn btn-primary">Download&nbsp;&nbsp;<i class='far fa-arrow-alt-circle-down'></i></button>
					
							</div>
						</div>
					</form>
					<br>
		<?php	
				}
			}
		?>

		</div>



    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js" integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em" crossorigin="anonymous"></script>
  </body>
</html>
