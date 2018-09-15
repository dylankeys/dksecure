<?php
	session_start();
	include("../config.php");
	include("lib.php");

	$error = "This password has expired or has been viewed the maximum amount of times and is no longer available.";
	
	if (isset($_GET["id"])) {
		$hash = $_GET["id"];
		
		$dbQuery=$db->prepare("select * from passwords where hash=:hash");
		$dbParams = array('hash'=>$hash);
		$dbQuery->execute($dbParams);
		$fileCount = $dbQuery->rowCount();

		if ($fileCount < 1) {
			header("Location: ../pw?error=" . $error);
		}
		
		$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
		
		$secretID = $dbRow["id"];
		$secret = $dbRow["secret"];
		$accessCount = $dbRow["access_count"];

		if ($accessCount < 1) {
			header("Location: ../pw?error=" . $error);
		}

	}
	else if (isset($_POST["secret"])) {
		$hash = bin2hex(mcrypt_create_iv(11, MCRYPT_DEV_URANDOM));
		$newSecret = $_POST["secret"];
		$expiryTime = $_POST["expire"];
		$attempts = 3;
		$time = time();
		
		$dbQuery=$db->prepare("insert into passwords values (null,:secret,:hash,:attempts,:time,:expirytime)");
		$dbParams = array('secret'=>$newSecret, 'hash'=>$hash, 'attempts'=>$attempts, 'time'=>$time, 'expirytime'=>$expiryTime);
		$dbQuery->execute($dbParams);

		header("Location: ../pw?status=success&hash=" . $hash);
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

		<title>SharePass</title>

		<!-- Bootstrap core CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css" integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous">

		<!-- Font Awesome CSS -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">

		<!-- DK CSS -->
		<link href="../css/styles.css" rel="stylesheet">

		<script>
			function setClipboard(value) {
				var tempInput = document.createElement("input");
				tempInput.style = "position: absolute; left: -1000px; top: -1000px";
				tempInput.value = value;
				document.body.appendChild(tempInput);
				tempInput.select();
				document.execCommand("copy");
				document.body.removeChild(tempInput);
				document.getElementById("copy_secret").innerHTML = "Copied!";
			}
  		</script>
	</head>

	<body>

		<div class="container" style="padding-top: 20px;">
			<nav class="navbar navbar-expand-lg navbar-light bg-light">
				<a class="navbar-brand" href="../">SharePass</a>
				<ul class="navbar-nav mr-auto">
				</ul>
		  	</nav>
		  	<br>
		  	<?php
		  		if (isset($secret) && isset($_POST["access"]) && $_POST["access"] == 1) {
		  			if (!empty($_POST["email"])) {
		  				$error = "Password access error, please try again.";
		  				echo "<script>window.location.href = '../pw?error=".$error."'</script>";
		  			}
		  			else {
		  				secretAccess($secretID);
		  	?>
				  		<div class="card">
							<div class="card-body">
								<h5 class="card-title">Your password</h5>
								<input class="form-control" type="text" name="secret" value="<?php echo $secret; ?>" readonly><br>

								<button style="float:right" onclick="setClipboard('<?php echo $secret; ?>')" class="btn btn-primary"><p id="copy_secret">Copy&nbsp;&nbsp;<i class="far fa-clone"></i></p></button>
						
							</div>
						</div>

		  	<?php
		  			}
		  		}
		  		else if (isset($secret)) {
		 	?>
	 			<div class="card">
					<div class="card-body">
						<h5 class="card-title">Your password</h5>
						<form action="?id=<?php echo $hash; ?>" method="post">
							<input type="hidden" name="access" value="1">
							<input type="text" name="email" id="email" class="access-check">

							<button class="btn btn-primary btn-block"><p id="copy_secret">Reveal password&nbsp;&nbsp;<i class="fas fa-magic"></i></p></button>
						</form>
					</div>
				</div>

		 	<?php
		  		}
		  		else if (isset($_GET["status"]) && $_GET["status"] == "success") {
		  			$seret_url = "http://secure.dylankeys.com/pw?id=".$_GET['hash'];
		  		?>
			  		<div class="card">
						<div class="card-body">
							<h5 class="card-title">Share password</h5>
							<input class="form-control" type="text" name="secret" value="<?php echo $seret_url; ?>" readonly><br>

							<button style="float:right" onclick="setClipboard('<?php echo $seret_url; ?>')" class="btn btn-primary"><p id="copy_secret">Copy&nbsp;&nbsp;<i class="far fa-clone"></i></p></button>
					
						</div>
					</div>
				<?php
		  		}
		  		else {
		  	?>
		  	<form method="post" action="index.php">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">Set password</h5>
						
						<div class="form-row">
    						<div class="form-group col-md-8">
								<input class="form-control" type="text" name="secret" placeholder="Set password" required>
							</div>
							<div class="form-group col-md-4">
								<select class="form-control" name="expire" required>
									<option value="-1 day">Day</option>
									<option value="-1 week" selected>Week</option>
									<option value="-2 weeks">Fortnight</option>
								</select>
							</div>
						</div>
						
						<button style="float:right" type="submit" class="btn btn-primary">Share&nbsp;&nbsp;<i class="fas fa-share-square"></i></button>
			
					</div>
				</div>
			</form>

		  	<?php
		  		}
			?>
			<br>
		</div>



    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js" integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em" crossorigin="anonymous"></script>
  </body>
</html>
