<?php
        session_destroy();
        include("config.php");
        
        $hash = $_POST["hash"];

        $dbQuery=$db->prepare("select * from secure_files where hash=:hash");
        $dbParams = array('hash'=>$hash);
        $dbQuery->execute($dbParams);
        $dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);

        $file=$dbRow["pathtofile"];

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