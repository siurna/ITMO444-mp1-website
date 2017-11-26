<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

## Basic AWS dependencies
require 'vendor/autoload.php';
use Aws\Rds\RdsClient;

$rdsClient = new RdsClient([ 'region' => 'us-east-1', 'version' => '2014-09-01']);
$rdsURL = "create new";
$rdsConnection = null;

function getRDShost(){
	global $rdsClient, $rdsURL;

	$rdsInstances = $rdsClient->describeDBInstances();

	foreach ($rdsInstances["DBInstances"] as $rds)
		if ($rds["DBInstanceIdentifier"] == "mp1instancesiurna"){
			$rdsURL = $rds["Endpoint"]["Address"];	
			return;
		}
}

function createRDS(){
	global $rdsClient, $rdsConnection;

	$rdsClient->createDBInstance([
		'AllocatedStorage' => 5,
		'DBInstanceClass' => 'db.t2.micro',
		'Engine' => 'MySQL',
		'DBInstanceIdentifier' => 'mp1instancesiurna',
		'DBName' => 'mp1rdsSiurna',
		'MasterUsername' => 'tsiurna',
		'MasterUserPassword' => 'akmjljjl2048',
	]);

	getRDShost();
	$rdsConnection = new mysqli($rdsURL, "tsiurna", "akmjljjl2048");

	if ($rdsConnection->connect_error)
    	die("Connection failed: " . $rdsConnection->connect_error);

    $rdsConnection->query("CREATE TABLE records (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, email VARCHAR(32), phone VARCHAR(32), s3-raw-url VARCHAR(32), s3-finished-url VARCHAR(32), status INT(1), reciept BIGINT)");
}

getRDShost();
	if ($rdsURL == "create new")
		createRDS();

print_r($rdsURL);
print_r($rdsConnection);
?>