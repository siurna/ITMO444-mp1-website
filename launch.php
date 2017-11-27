<?php
	require("init.php");

	/* S3 buckets */
	if (!empty($s3CreateBuckets = getS3BucketsNeeded())){
		foreach ($s3CreateBuckets as $createBucket) {
			$s3BucketName = uniqid($awsPrefix.$createBucket);
			
			$s3Client->waitUntil("BucketExists", ["Bucket" => $s3BucketName]);
		}
	}

	/* RDS database */
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

		connectToRDSInstance();

	    $rdsConnection->query("CREATE TABLE IF NOT EXISTS records (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `email` VARCHAR(32), `phone` VARCHAR(32), `s3-raw-url` VARCHAR(100), `s3-finished-url` VARCHAR(100), `status` INT(1), `receipt` BIGINT);");
	}
?>