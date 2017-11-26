<?
	error_reporting(E_ALL);

	require 'vendor/autoload.php';
	use Aws\Rds\RdsClient;

	$rdsClient = new RdsClient([
		'region' => 'us-east-1',
		'version' => '2014-09-01'
	]);

	// Settings
	$rdsUser = "tsiurna";
	$rdsPass = "akmjljjlnnv2018";
	$rdsRegion = "us-east-1";
	$rdsIdentifier = "mp1siurna".uniqid();
?>