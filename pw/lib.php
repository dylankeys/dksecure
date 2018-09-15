<?php

//SharePass functions

function secretAccess($id) {
	include("../config.php");
	
	$dbQuery=$db->prepare("update passwords set access_count = access_count - 1 where id=:id");
	$dbParams = array('id'=>$id);
	$dbQuery->execute($dbParams);
}