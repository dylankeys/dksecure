<?php
        include("config.php");
        
        $fileid = $_POST["fileid"];
        $hash = $_POST["hash"];
        $user = $_POST["user"];
		$time = time();

        $dbQuery=$db->prepare("select filename from files where id=:fileid");
        $dbParams = array('fileid'=>$fileid);
        $dbQuery->execute($dbParams);
        $dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);

        $file = $dataroot . $dbRow["filename"];
		
		$dbQuery=$db->prepare("insert into downloads values(null,:fileid,:user,:time)");
        $dbParams = array('fileid'=>$fileid, 'user'=>$user, 'time'=>$time);
        $dbQuery->execute($dbParams);
        $dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
		
		$_SESSION["download"] = 1;

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }