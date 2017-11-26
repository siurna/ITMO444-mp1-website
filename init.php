<?php
	error_reporting(E_ALL);
	require 'vendor/autoload.php';
	
	$awsRegion = "us-east-1";
	$awsPrefix = "siurna-";

	/* S3 settings */
	use Aws\S3\S3Client;
	$s3Client = new Aws\S3\S3Client([
	    'version' => '2006-03-01',
	    'region'  => $awsRegion
	]);

	$s3BucketsNeeded = array("color", "grayscale");

	function getS3BucketsNeeded(){
		global $s3BucketsNeeded, $s3Client;

		$result = array();
		$s3Buckets = $s3Client->listBuckets();

		foreach ($s3BucketsNeeded as $bucketNeeded){
			$bucketLacking = true;
			foreach ($s3Buckets as $bucket)
				if (strpos($bucket[0]["Name"], $bucketNeeded) !== false)
					$bucketLacking = false;

			if ($bucketLacking)
				array_push($result, $bucketNeeded);
		}

		return $result;
	}

	/* RDS settings */
	use Aws\Rds\RdsClient;
	$rdsClient = new RdsClient([
		'region' => $awsRegion,
		'version' => '2014-10-31'
	]);

	$rdsIdentifier = $awsPrefix.uniqid();
	$rdsURL = false;
	
	$rdsUser = "tsiurna";
	$rdsPass = "akmjljjlnnv2018";
	$rdsDatabase = "mp1siurna";

	function getRDShost(){
		global $rdsClient, $rdsURL;

		$rdsInstances = $rdsClient->describeDBInstances();

		foreach ($rdsInstances["DBInstances"] as $rds)
			if (strpos($rds["DBInstanceIdentifier"], $awsPrefix) !== false){
				$rdsURL = $rds["Endpoint"]["Address"];
				return $rdsURL;
			}

		return false;
	}
?>