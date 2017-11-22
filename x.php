<?php
     	error_reporting(E_ALL);
        ini_set('display_errors', 1);

        require 'vendor/autoload.php';
        use Aws\Ec2\Ec2Client;
        use Aws\Credentials\CredentialProvider;
use Aws\Common\Credentials\Credentials;

$provider = CredentialProvider::instanceProfile();
// Be sure to memoize the credentials
$memoizedProvider = CredentialProvider::memoize($provider);


        $ec2Client = new Ec2Client([
            'region' => 'us-west-2',
            'version' => '2016-11-15',
            'profile' => 'default',
            'credentials' => $memoizedProvider
        ]);
	$result = $ec2Client->describeInstances();
        var_dump($result);
        echo "SOMEThING";
?>