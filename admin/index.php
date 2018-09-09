<?php
  session_start();
  include("config.php");

  if(isset($_GET["id"])) {
    $hash = $_GET["id"];
    
    $dbQuery=$db->prepare("select * from secure_files where hash=:hash");
    $dbParams = array('hash'=>$hash);
    $dbQuery->execute($dbParams);
    $fileCount = $dbQuery->rowCount();

    if($fileCount < 1) {
      $error = "File does not exist.";
    }
    else {
      $dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);

      $filename=$dbRow["filename"];
      $auth=$dbRow["auth"];

      if(isset($_SESSION["user"])) { 
        $user = $_SESSION["user"];
      }
      else {
        header("Location: auth/?id=" . $hash);
      }

      $authenticatedUsers = explode(",", $auth);

      foreach ($authenticatedUsers as $authenticatedUser) {
        if($authenticatedUser == $user) {
          $_SESSION["auth"] = 1;
        }
      }

      if($_SESSION["auth"] != 1)
      {
        header("Location: auth/?error=You%20do%20not%20have%20permission%20to%20access%20this%20file&id=" . $hash);
      }
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

    <title>DK Dev</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css" integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">

    <!-- DK CSS -->
    <link href="../css/styles.css" rel="stylesheet">

  </head>

  <body>

    <div class="container" style="padding-top: 20px;">
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">DK Secure</a>
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

      <?php
      if (isset($error)) {
        echo "<h1 style='color:red'>".$error."</h1>";
      }
      else {
      ?>
      <form method="post" action="download.php">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"><?php echo $filename; ?></h5>
            
            
            <i class="far fa-folder-open fa-5x"></i>
            
            <input type="hidden" name="hash" value="<?php echo $hash; ?>">
            <button style="float:right" type="submit" class="btn btn-primary">Download&nbsp;<i class='far fa-arrow-alt-circle-down'></i></button>
            
          </div>
        </div>
      </form>
      <?php
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
