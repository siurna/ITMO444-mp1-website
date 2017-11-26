<?php
require("init.php");

/* Sets up S3 buckets */
if (!empty($s3CreateBuckets = getS3BucketsNeeded())){
	foreach ($s3CreateBuckets as $createBucket) {
		$s3BucketName = uniqid($awsPrefix.$createBucket, true);
		echo "Creating bucket named {$s3BucketName}\n";
		
		$s3Client->createBucket(['Bucket' => $bucket]);
		$s3Client->waitUntil('BucketExists', ['Bucket' => $bucket]);
	}
}

die;

/* Sets up RDS database */
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