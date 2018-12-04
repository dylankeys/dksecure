<?php
	session_start();
    include("../config.php");
	include("../lib.php");

    unset($_SESSION["user"]);
    unset($_SESSION["auth"]);

    if (isset($_POST["action"]) && $_POST["action"]=="login") {
        $_SESSION["user"] = $_POST["email"];
		$hash = $_POST["hash"];
		
		send_verification($_SESSION["user"], $hash);
		
		$title = 'Enter verification code to access file (ID: '.$_POST["hash"].')';
		$icon = '<i class="fas fa-lock"></i>';
		$submit = 'Access&nbsp;&nbsp;<i class="fas fa-unlock"></i>';
		$action = 'verify';
		$field = 'code';
		$placeholder = 'Verification code';
		
    }
	else if (isset($_POST["action"]) && $_POST["action"]=="verify") {
        $dbQuery=$db->prepare("select vericode from verification where email=:email and hash=:hash");
		$dbParams = array('email'=>$_SESSION["user"], 'hash'=>$_POST["hash"]);
		$dbQuery->execute($dbParams);
		$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
		
		$vericode = $dbRow["vericode"];
		$usercode = $_POST["code"];
		
		/*if ($usercode == $vericode) {
			$dbQuery=$db->prepare("delete from verification where vericode=:vericode");
			$dbParams = array('vericode'=>$vericode);
			$dbQuery->execute($dbParams);
			
			header("Location: ../vault/?id=" . $_POST["hash"]);
		}
		else {
			$error = "Verification code was not valid, please try again.";
			header("Location: ../?error=" . $error);
		}*/
    }
	else if(isset($_GET["id"])) {
		$hash = $_GET["id"];
		
		$dbQuery=$db->prepare("select * from files where hash=:hash");
		$dbParams = array('hash'=>$hash);
		$dbQuery->execute($dbParams);
		$fileCount = $dbQuery->rowCount();
		
		$title = 'Enter email address (ID: '.$hash.')';
		$icon = '<i class="fas fa-user-circle"></i>';
		$submit = 'Send verification';
		$action = 'login';
		$field = 'email';
		$placeholder = 'Email';

		if($fileCount < 1) {
			$error = "File does not exist. Please enter a valid file ID below.";
			header("Location: ../?error=" . $error);
		}
	}
	else {
		$error = "File ID not set. Please enter a valid file ID below.";
		header("Location: ../?error=" . $error);
	}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
	<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js" integrity="sha384-SlE991lGASHoBfWbelyBPLsUlwY1GwNDJo3jSJO04KZ33K2bwfV9YBauFfnzvynJ" crossorigin="anonymous"></script>

	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../images/favicon.ico">
	
    <title>DK Secure | Auth</title>
	
	<!--DK CSS-->
	<link href="../css/styles.css" rel="stylesheet">
	
	</head>

	<body>

		<div class="container" style="padding-top: 20px;">
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="../">DK Secure</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <!--<span class="logged-in">
            <p>Logged in as Dylan Keys</p>
          </span>-->
        </div>
      </nav>
      <br>

	<div class="login">

    <?php
	
    if (isset($_GET["error"])) {
    
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<strong>Error!</strong> '. $_GET["error"] .'
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>';
    }
	else if (isset($_SESSION["user"])) {
		echo '<div class="alert alert-primary alert-dismissible fade show" role="alert">
				<strong><i class="fas fa-info-circle"></i></strong> A verification code has been sent to your email address. Please enter this below.
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>';
	}
	echo 'Vericode: '.$vericode.' Usercode: '.$usercode;
?>
	
		<form name="login" method="post" action="index.php">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title">
						<?php echo $title; ?>
					</h5>
					
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="user-addon"><?php echo $icon; ?></span>
						</div>
						<input type="text" class="form-control" placeholder="<?php echo $placeholder; ?>" name="<?php echo $field; ?>" aria-describedby="user-addon">
					</div>
					
					<input type="hidden" name="hash" value="<?php echo $hash; ?>">
					<input type="hidden" name="action" value="<?php echo $action; ?>">
					<button style="float:right" type="submit" class="btn btn-primary"><?php echo $submit; ?></button>
					
				</div>
			</div>
		</form>
	</div>

   </div>
   </div>
	  
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>
</body>

</html>