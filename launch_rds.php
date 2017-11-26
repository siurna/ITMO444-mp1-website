<?php
require("init.php");

$rdsIdentifier = "mp1siurna".uniqid();
getRDShost();

//if ($rdsURL == "create new"){ 
	echo shell_exec('aws rds create-db-instance --db-instance-identifier '.$rdsIdentifier.' --allocated-storage 5 --db-instance-class db.t2.micro --engine mysql --master-username '.$rdsUsername.' --master-user-password '.$rdsPassword.' --db-name mp1rdssiurna --region '.$rdsRegion.';	aws rds wait db-instance-available --db-instance-identifier '.$rdsIdentifier);

	$rdsConnection = new mysqli($rdsURL, $rdsUsername, $rdsPassword);
    $rdsConnection->query("CREATE TABLE IF NOT EXISTS records (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, email VARCHAR(32), phone VARCHAR(32), s3-raw-url VARCHAR(32), s3-finished-url VARCHAR(32), status INT(1), reciept BIGINT)");

    print_r($rdsURL);
//}
?>