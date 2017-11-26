<?php
require("init.php");

// Sets up database
if (!getRDShost()){ 
	$rdsClient->createDBInstance([
		'AllocatedStorage' => 5,
		'DBInstanceClass' => 'db.t2.micro',
		'Engine' => 'MySQL',
		'DBInstanceIdentifier' => $rdsIdentifier,
		'DBName' => $rdsDatabase,
		'MasterUsername' => $rdsUser,
		'MasterUserPassword' => $rdsPass,
	]);

	$rdsClient->waitUntil('DBInstanceAvailable', [
		'DBInstanceIdentifier' => $rdsIdentifier
	]);

	getRDShost();

	$rdsConnection = new mysqli($rdsURL, $rdsUser, $rdsPass, $rdsDatabase, 3306);
    $rdsConnection->query("CREATE TABLE IF NOT EXISTS records (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `email` VARCHAR(32), `phone` VARCHAR(32), `s3-raw-url` VARCHAR(32), `s3-finished-url` VARCHAR(32), `status` INT(1), `reciept` BIGINT);");
}
?>