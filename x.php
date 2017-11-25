<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	## Basic AWS dependencies
    require 'vendor/autoload.php';

	use Aws\Rds\RdsClient;

	$rdsClient = new RdsClient([
		'region' => 'us-east-1',
		'version' => '2014-09-01'
    ]);


	## Checking if Database is prepared
	$rdsInstances = $rdsClient->describeDBInstances();

	foreach ($rdsInstances["DBInstances"] as $rds) 
		if ($rds["DBInstanceIdentifier"] == "mp1instanceSiurna")
			$rdsExists = $rds["Endpoint"]["Address"];
	
	if (!isset($rdsExists)){
		$rds_result = $rdsClient->createDBInstance([
			'AllocatedStorage' => 1,
			'DBInstanceClass' => 'db.t2.micro',
			'Engine' => 'MySQL',
			'DBInstanceIdentifier' => 'mp1instanceSiurna',
			'DBName' => 'mp1rdsSiurna',
			'MasterUserPassword' => 'tsiurna',
			'MasterUsername' => 'akmjljjl2048',
		]);
	}

	var_dump($rds_result);
?>