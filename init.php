<?php
	error_reporting(E_ALL);
	require 'vendor/autoload.php';
	$awsRegion = "us-east-1";

	// S3 settings
	/*
	$s3 = new Aws\S3\S3Client([
	    'version' => '2014-10-31',
	    'region'  => $awsRegion
	]);
	*/

	// RDS settings
	use Aws\Rds\RdsClient;

	$rdsClient = new RdsClient([
		'region' => $awsRegion,
		'version' => '2014-10-31'
	]);

	$rdsIdentifier = "mp1siurna".uniqid();
	$rdsURL = false;
	
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