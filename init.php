<?php
	/* General AWS settings */
	require 'vendor/autoload.php';

	$awsRegion = "us-east-1";
	$awsPrefix = "siurna-";

	/* S3 settings */
	use Aws\S3\S3Client;
	$s3Client = new Aws\S3\S3Client([
	    'version' => '2006-03-01',
	    'region'  => $awsRegion
	]);

	$s3BucketsNeeded = array("color-", "grayscale-");

	function describeS3Bucket($nameNeeded){
		global $s3Client;

		$s3Buckets = $s3Client->listBuckets();

		foreach ($s3Buckets["Buckets"] as $bucket)
			if (strpos($bucket["Name"], $nameNeeded) !== false)
				return $bucket["Name"];

		return false;
	}

	function getS3BucketsNeeded(){
		global $s3BucketsNeeded;

		$result = array();

		foreach ($s3BucketsNeeded as $bucketNeeded)
			if (!describeS3Bucket($bucketNeeded))
				array_push($result, $bucketNeeded);

		return $result;
	}

	/* RDS settings */
	use Aws\Rds\RdsClient;
	$rdsClient = new RdsClient([
		'region' => $awsRegion,
		'version' => '2014-10-31'
	]);

	$rdsURL = false;
	
	$rdsUser = "tsiurna";
	$rdsPass = "akmjljjlnnv2018";
	$rdsDatabase = "mp1siurna";

	function getRDShost(){
		global $rdsClient, $rdsURL, $awsPrefix;

		$rdsInstances = $rdsClient->describeDBInstances();

		foreach ($rdsInstances["DBInstances"] as $rds)
			if (strpos($rds["DBInstanceIdentifier"], $awsPrefix) !== false){
				$rdsURL = $rds["Endpoint"]["Address"];
				return $rdsURL;
			}

		return false;
	}

	function connectToRDSInstance(){
		global $rdsConnection, $rdsURL, $rdsUser, $rdsPass, $rdsDatabase;

		getRDShost();
		$rdsConnection = new mysqli($rdsURL, $rdsUser, $rdsPass, $rdsDatabase, 3306);

		// In case table does not exist. I know, not the best solution... but hey, it works!
	    $rdsConnection->query("CREATE TABLE IF NOT EXISTS records (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `email` VARCHAR(32), `phone` VARCHAR(32), `s3-raw-url` VARCHAR(100), `s3-finished-url` VARCHAR(100), `status` INT(1), `receipt` BIGINT);");

		return true;
	}
?>