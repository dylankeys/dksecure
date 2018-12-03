<?php

function send_verification($email, $hash) {
	
	include("config.php");
	
	$vericode = rand(0,9).rand(0,9).rand(0,9).rand(0,9);
	
	$dbQuery=$db->prepare("insert into verification values (null,:email,:hash,:vericode)");
	$dbParams = array('email'=>$email, 'hash'=>$hash, 'vericode'=>$vericode);
	$dbQuery->execute($dbParams);
	
	$to = $email;
	$subject = "DK Secure // Verification";

	$message = "
	<html>
	<head>
	<title>DK Secure // Verification</title>
	</head>
	<body>
	<p>Please use the following verification on the DK Secure application to confirm your identity</p>
	<p><strong>".$vericode."</strong></p>
	</body>
	</html>
	";

	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	$headers .= 'From: <admin@dylankeys.com>' . "\r\n";

	mail($to,$subject,$message,$headers);
}