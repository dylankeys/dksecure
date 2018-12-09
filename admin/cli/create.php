<?php
#!/usr/bin/env php
#Author: Dylan Keys
#Adds file to application

define('CLI_SCRIPT', true);

include("config.php");

$longopts  = array(
    "filename::",
    "auth::",    
	"group::",	
    "help",
);

$options = getopt($longopts);

if (isset($options['help']) || !isset($options['filename'])) {
  echo "Add a file to the secure download application\n";
  echo "=============================================\n";
  echo "Options\n";
  echo "=============================================\n";
  echo "-f    Filename\n";
  echo "-a    Email addresses (comma separated list)\n";
  echo "-h    Help\n";
  echo "-g    Group (enter hash)\n";
  echo "=============================================\n";
}
else {
	if (isset($options['filename'])) {
		$file=$options['filename'];
	}
	else {
	  echo "ERROR: --filename must be set to add a new file\n";
	  exit(1);
	}

	if (isset($options['auth'])) {
	  $auth=$options['auth'];
	}
	else {
	  echo "ERROR: --auth must be set to add a new file\n";
	  exit(1);
	}

	if (isset($options['group'])) {
	  $hash=$options['group'];
	}
	else {
	  $hash = bin2hex(mcrypt_create_iv(11, MCRYPT_DEV_URANDOM));
	}
}



$dbQuery=$db->prepare("insert into users values (null,:file,:auth,:hash");
$dbParams = array('file'=>$file,'auth'=>$auth,'hash'=>$hash);
$dbQuery->execute($dbParams);

echo "SUCCESS! The file has been added to database\n";
echo "Authorised users: " . $auth . "\n";
echo "Hash (can be used to group files): " . $hash . "\n";
