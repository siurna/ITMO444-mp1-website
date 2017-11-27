<?php
	require("init.php");

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
				<div class="alert alert-warning" style="margin-bottom: 40px;">
					<h4>Heads up!</h4>
					<p>Database for this website is still being loaded, so gallery capability might be restricted. Sorry for any inconveniences and please check again later!</p>
					<p><a href="gallery.php" class="btn btn-outline-info">Reload this page</a></p>
				</div>
				<?php endif;?>
				
				<div class="well">
					<h1>Gallery</h1><hr/>

					<?php
						if (getRDShost()){
							connectToRDSInstance();

							print_r($rdsConnection->query("SELECT * FROM records;"));
						}
					?>
				</div>
			</div>
		</div>
	</div>
</body>
</html>