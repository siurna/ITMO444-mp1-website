<?php
	require("init.php");

	/* Processing form submission */
	if (isset($_FILES['image']['tmp_name'])){
		$colorBucket = describeS3Bucket("color");
		$bwBucket = describeS3Bucket("grayscale");

		$filename = uniqid("img-");
		$extention = ".".pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

		$s3Client->putObject([
			'Bucket' => $colorBucket,
			'Key'    => $filename.$extention,
			'SourceFile' => $_FILES['image']['tmp_name'],
		]);
		$colorUrl = $s3Client->getObjectUrl($colorBucket, $filename.$extention);

		// Generating BW image

		$im = imagecreatefrompng($_FILES['image']['tmp_name']);
    	imagepng($im, "/var/www/html/tmp_img/".$filename.".png");

		$s3Client->putObject([
			'Bucket' => $bwBucket,
			'Key'    => ($filename.".png"),
			'SourceFile' => "/var/www/html/tmp_img/".$filename.".png",
		]);
		$bwUrl = $s3Client->getObjectUrl($bwBucket, $filename.".png");
		imagedestroy($im);


		// mySQL query
		if (getRDShost()){
			connectToRDSInstance();

			$query = $rdsConnection->prepare("INSERT INTO records (id, email, phone, s3-raw-url, s3-finished-url, status,reciept) VALUES (NULL,?,?,?,?,?,?)");

			if (!$query){
				echo "Prepare failed: (".$rdsConnection->errno.") ".$rdsConnection->error;
			}else{
				echo $query->bind_param("ssssii", $_POST["email"], $_POST["phone"], $colorUrl, $bwUrl, 1, uniqid());
			}
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://bootswatch.com/4/sketchy/bootstrap.min.css">
	<title>Upload – Gallery</title>

	<style>
		.container{
			padding-top: 5%;
			background-color: transparent;
		}

		body{
			background-color: #f3f3f4;
		}

		.well{
			border-radius: 4px;
			background-color: white;
			padding: 30px 30px 30px 30px;
		}

		hr{
			opacity: 0.4;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-lg-8 offset-lg-2 text-center">
				<?php if (!getRDShost()):?>
				<div class="alert alert-dismissible alert-warning" style="margin-bottom: 40px;">
					<h4>Heads up!</h4>
					<p>Database for this website is still being loaded, so upload capability might be restricted. Sorry for any inconveniences and please check again later!</p>
					<p><a href="upload.php" class="btn btn-outline-info">Reload this page</a></p>
				</div>
				<?php endif;?>
				
				<div class="well">
					<h1>Upload image</h1><hr/>

					<form action="" method="POST" enctype="multipart/form-data">
						<div class="row">
							<div class="col-lg-6">
								<input type="email" name="email" class="input-lg form-control" placeholder="Your email address">
							</div>

							<div class="col-lg-6">
								<input name="phone" class="input-lg form-control" placeholder="Your phone">
							</div>
						</div>

						<div class="row">
							<div class="text-center col-lg-12" style="margin-top: 25px;">
								<label for="image" style="margin-right: 10px">Choose file to upload</label>
								<input type="file" id="image" name="image">
							</div>
						</div>

						<hr/>
						<button class="btn btn-success btn-lg">Let's do it!</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>
</html>