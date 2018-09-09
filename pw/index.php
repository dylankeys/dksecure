<?php
	session_start();
	include("../config.php");

	
	if(isset($_GET["id"])) {
		$hash = $_GET["id"];
		
		$dbQuery=$db->prepare("select * from pw where hash=:hash");
		$dbParams = array('hash'=>$hash);
		$dbQuery->execute($dbParams);
		$fileCount = $dbQuery->rowCount();

		if($fileCount < 1) {
			$error = "This password has expired or has been viewed the maximum amount of times and is no longer available.";
			header("Location: ../pw?error=" . $error);
		}
		
		$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
		
		$secret = $dbRow["secret"];
	}
	else if (isset($_POST["secret"])) {
		$hash = bin2hex(mcrypt_create_iv(11, MCRYPT_DEV_URANDOM));
		$new_secret = $_POST["secret"];
		$attempts = 3;
		$time = time();
		
		$dbQuery=$db->prepare("insert into passwords values (null,:secret,:hash,:attempts,:time)");
		$dbParams = array('secret'=>$secret, 'hash'=>$hash, 'attempts'=>$attempts, 'time'=>$time);
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

		<title>DK Secure | Vault</title>

		<!-- Bootstrap core CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css" integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous">

		<!-- Font Awesome CSS -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">

		<!-- Custom styles for this template -->
		<link href="css/pricing.css" rel="stylesheet">
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
		  		if (isset($secret)) {
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
		  		else if (isset($status) && $status == "success") {
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
						
						<input class="form-control" type="text" name="secret" placeholder="Set password" required><br>
						
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
