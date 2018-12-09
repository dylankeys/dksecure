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

$options = getopt('', $longopts);

if (isset($options['help']) || !isset($options['filename'])) {
  echo "Add a file to the secure download application\n";
  echo "=============================================\n";
  echo "Options\n";
  echo "=============================================\n";
  echo "--filename	Filename\n";
  echo "--auth		Email addresses (comma separated list)\n";
  echo "--group		Group (enter existing hash to group, otherwise new hash will be generated)\n";
  echo "--help		Help\n";
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
	
	$dbQuery=$db->prepare("insert into `files` values (null,:hash,:file)");
	$dbParams = array('file'=>$file,'hash'=>$hash);
	$dbQuery->execute($dbParams);
	
	$dbQuery=$db->prepare("insert into `auth` values (null,:hash,:auth)");
	$dbParams = array('auth'=>$auth,'hash'=>$hash);
	$dbQuery->execute($dbParams);

	echo "SUCCESS! The file has been added to database\n";
	echo "Authorised users: " . $auth . "\n";
	echo "Hash (can be used to group files): " . $hash . "\n";
	echo "Secure URL: https://secure.dylankeys.com/auth/?id=" . $hash . "\n";
}
