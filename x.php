<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	require 'vendor/autoload.php';
	use Aws\Ec2\Ec2Client;

	$ec2Client = new Ec2Client([
		'region' => 'us-east-1',
		'version' => '2016-11-15'
	]);
	
	$result = $ec2Client->describeInstances();
	var_dump($result);
	echo "SOMEThING";
?>