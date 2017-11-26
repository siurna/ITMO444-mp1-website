<?php
error_reporting(E_ALL);

require 'vendor/autoload.php';
use Aws\Rds\RdsClient;

$rdsClient = new RdsClient([
	'region' => 'us-east-1',
	'version' => '2014-09-01'
]);

$rdsURL = "create new";

function getRDShost(){
	global $rdsClient, $rdsURL;

	$rdsInstances = $rdsClient->describeDBInstances();

	foreach ($rdsInstances["DBInstances"] as $rds)
		if (strpos($rds["DBInstanceIdentifier"], "mp1siurna") !== false){
			$rdsURL = $rds["Endpoint"]["Address"];
			return;
		}
}

getRDShost();

if ($rdsURL == "create new"){
	$identifier = "mp1siurna".uniqid();
 
 	## This one was troubling cause iam instance profiles...
	//shell_exec('aws rds create-db-instance --db-instance-identifier {$identifier} --allocated-storage 5 --db-instance-class db.t2.micro --engine mysql --master-username tsiurna --master-user-password akmjljjl2048 --db-name mp1rdssiurna;	aws rds wait db-instance-available --db-instance-identifier "{$identifier}"');

	$rdsClient->createDBInstance([
		'AllocatedStorage' => 1,
		'DBInstanceClass' => 'db.t2.micro',
		'Engine' => 'MySQL',
		'DBInstanceIdentifier' => $identifier,
		'DBName' => 'mp1rdssiurna',
		'MasterUsername' => 'tsiurna',
		'MasterUserPasword' => 'akmjljjl2048',
	]);

	while ($rdsURL == "create new"){
		sleep(3);
		getRDShost();
		print_r("Waiting for RDS...\n");
	}

	$rdsConnection = new mysqli($rdsURL, "tsiurna", "akmjljjl2048");
    $rdsConnection->query("CREATE TABLE IF NOT EXISTS records (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, email VARCHAR(32), phone VARCHAR(32), s3-raw-url VARCHAR(32), s3-finished-url VARCHAR(32), status INT(1), reciept BIGINT)");

    print_r($rdsURL);
}
?>