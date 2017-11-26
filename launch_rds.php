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
		if ($rds["DBInstanceIdentifier"] == "mp1instancesiurna"){
			$rdsURL = $rds["Endpoint"]["Address"];	
			return;
		}
}

getRDShost();

if ($rdsURL == "create new"){
	exec('aws rds create-db-instance --db-instance-identifier mp1instancesiurna --allocated-storage 5 --db-instance-class db.t2.micro --engine mysql --master-username tsiurna --master-user-password akmjljjl2048 --db-name mp1rdssiurna;	aws rds wait db-instance-available --db-instance-identifier "mp1instancesiurna"');

	getRDShost();
	$rdsConnection = new mysqli($rdsURL, "tsiurna", "akmjljjl2048");

    $rdsConnection->query("CREATE TABLE records (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, email VARCHAR(32), phone VARCHAR(32), s3-raw-url VARCHAR(32), s3-finished-url VARCHAR(32), status INT(1), reciept BIGINT)");
}
?>