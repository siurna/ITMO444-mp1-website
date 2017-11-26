<?
	error_reporting(E_ALL);
	require 'vendor/autoload.php';

	// RDS settings
	use Aws\Rds\RdsClient;

	$rdsClient = new RdsClient([
		'region' => 'us-east-1',
		'version' => '2014-09-01'
	]);

	$rdsUser = "tsiurna";
	$rdsPass = "akmjljjlnnv2018";
	$rdsRegion = "us-east-1";

	$rdsURL = "create new";

	function getRDShost(){
		global $rdsClient, $rdsURL;

		$rdsInstances = $rdsClient->describeDBInstances();

		foreach ($rdsInstances["DBInstances"] as $rds)
			if (strpos($rds["DBInstanceIdentifier"], "mp1siurna") !== false){
				$rdsURL = $rds["Endpoint"]["Address"];
				return;
			}
	}
?>