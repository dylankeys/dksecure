<?php
#!/usr/bin/env php

define('CLI_SCRIPT', true);

include("../config.php");

//Have any passwords hit their time expiration?

//Array to hold any passwords to be deleted
$expiredPasswords = array();

//Select all passwords
$dbQuery=$db->prepare("select id, timecreated, expirytime from passwords");
$dbQuery->execute();

while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC)) {
	$passwordID = $dbRow["id"];
	$timecreated = strtotime($dbRow["timecreated"]);
	$expirytime = strtotime($dbRow["expirytime"]);

	//If a password's timecreated is below the expiretime then add to array for deletion 
	if($timecreated < $expirytime) {
		array_push($expiredPasswords, $passwordID);
	}
}

//If a password has been added to the array for deletion then delete from DB
if (count($expiredPasswords) > 0) {
	foreach ($expiredPasswords as $id) {
		$dbQuery=$db->prepare("delete from passwords where id=:id");
		$dbParams = array('id'=>$id);
		$dbQuery->execute($dbParams);
	}
}


