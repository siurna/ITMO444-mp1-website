<?php
	require("init.php");

	/* Processing form submission */
	if (isset($_FILES['image']['tmp_name'])){
		$filename = uniqid("img-").".";
		$urlsUploaded = array();

		$bwImage = imagecreatefrompng($_FILES['image']['tmp_name']);
    	imagepng($bwImage, "/var/www/html/tmp_img/".$filename.".png");

		$filesToUpload = array(
			0 => array(
				"Bucket" => describeS3Bucket("color"),
				"Key" => $filename.pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION),
				"SourceFile" => $_FILES['image']['tmp_name']
			),
			1 => array(
				"Bucket" => describeS3Bucket("grayscale"),
				"Key" => $filename."png",
				"SourceFile" => "/var/www/html/tmp_img/".$filename.".png"
			)
		);

		foreach ($filesToUpload as $upload) {
			$result = $s3Client->putObject($upload);			
			unset($upload["SourceFile"]); // Prepare for a waiter
			$s3Client->waitUntil('ObjectExists', $upload);

			array_push($urlsUploaded, $s3Client->getObjectUrl($upload["Bucket"], $upload["Key"]));
		}

		imagedestroy($bwImage);

		// mySQL query
		if (getRDShost()){
			$successUpload = true;

			connectToRDSInstance();

			$query = $rdsConnection->prepare("INSERT INTO `records` (`id`, `email`, `phone`, `s3-raw-url`, `s3-finished-url`, `status`, `receipt`) VALUES (NULL, ?, ?, ?, ?, ?, ?)");

			if (!$query){
				echo "Prepare failed: (".$rdsConnection->errno.") ".$rdsConnection->error;
			}else{
				$reciept = 1;
				echo $query->bind_param("ssssii", $_POST["email"], $_POST["phone"], $urlsUploaded[0], $urlsUploaded[1], $receipt, uniqid());
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
				<?php if (isset($successUpload)):?>
				<div class="alert alert-success" style="margin-bottom: 40px;">
					<h4>Hooray!</h4>
					<p>Photo uploaded successfully. Feel free to add more or visit our gallery section.</p>
					<p><a href="gallery.php" class="btn btn-outline-info">Gallery</a></p>
				</div>
				<?php endif;?>

				<?php if (!getRDShost()):?>
				<div class="alert alert-warning" style="margin-bottom: 40px;">
					<h4>Heads up!</h4>
					<p>Database for this website is still being loaded, so upload capability might be restricted. Sorry for any inconveniences and please check again later!</p>
					<p><a href="upload.php" class="btn btn-outline-info">Reload this page</a></p>
				</div>
				<?php endif;?>
				
				<div class="well">
					<h1>Upload an image</h1><hr/>

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