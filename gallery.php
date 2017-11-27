<?php
	require("init.php");

?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://bootswatch.com/4/sketchy/bootstrap.min.css">
	<title>Gallery – MP1</title>

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

	<script type="text/javascript">
		var $grid = $('.gallery').masonry({
		  columnWidth: ".image-dummy",
		  itemSelector: ".image"
		});

		// layout Masonry after each image loads
		$grid.imagesLoaded().progress( function() {
		  $grid.masonry('layout');
		});
	</script>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-lg-8 offset-lg-2 text-center">
				<?php if (!getRDShost()):?>
				<div class="alert alert-warning" style="margin-bottom: 40px;">
					<h4>Heads up!</h4>
					<p>Database for this website is still being loaded, so gallery capability might be restricted. Sorry for any inconveniences and please check again later!</p>
					<p><a href="gallery.php" class="btn btn-outline-info">Reload this page</a></p>
				</div>
				<?php endif;?>
				
				<div class="well">
					<h1>Gallery</h1> <a href="/upload.php" class="btn btn-primary btn-md">Upload a picture</a> <hr/>

					<div class="gallery row">
						<div class="col-lg-3 image-dummy"></div>
						<?php
							if (getRDShost()){
								connectToRDSInstance();

								$picures = $rdsConnection->query("SELECT `s3-raw-url`, `s3-finished-url` FROM records;");
								$colorBucket = describeS3Bucket("color");
								$bwBucket = describeS3Bucket("grayscale");

								foreach ($picures as $p) {
									$colorImg = $s3Client->getCommand('GetObject', [ "Bucket" => $colorBucket, "Key" => $p["s3-raw-url"] ]);
									$bwImg = $s3Client->getCommand('GetObject', [ "Bucket" => $bwBucket, "Key" => $p["s3-finished-url"] ]);

									//echo '<img src="'.$s3Client->createPresignedRequest($colorImg, '+1 day')->getUri().'"/>';
									echo '<div class="image"><img src="'.$s3Client->createPresignedRequest($bwImg, '+1 day')->getUri().'"/></div>';
								}
							}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/core.js"></script>
	<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js"></script>
</body>
</html>