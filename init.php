<?php
	error_reporting(E_ALL);
	require 'vendor/autoload.php';

	// S3 settings
	$s3 = new Aws\S3\S3Client([
    'version' => '2006-03-01',
    'region'  => 'us-west-2'
]);

	// RDS settings
	use Aws\Rds\RdsClient;

	$rdsClient = new RdsClient([
		'region' => 'us-east-1',
		'version' => '2014-09-01'
	]);

	$rdsIdentifier = "mp1siurna".uniqid();
	$rdsURL = false;
	$rdsRegion = "us-east-1";
	
	$rdsUser = "tsiurna";
	$rdsPass = "akmjljjlnnv2018";
	$rdsDatabase = "mp1siurna";


	function getRDShost(){
		global $rdsClient, $rdsURL;

		$rdsInstances = $rdsClient->describeDBInstances();

		foreach ($rdsInstances["DBInstances"] as $rds)
			if (strpos($rds["DBInstanceIdentifier"], "mp1siurna") !== false){
				$rdsURL = $rds["Endpoint"]["Address"];
				return $rdsURL;
			}

		return false;
	}
?>