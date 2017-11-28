<?php require("init.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://bootswatch.com/4/sketchy/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.10.0/css/lightbox.min.css" />

	<title>Gallery – MP1</title>

	<style>
		.container{
			padding-top: 5%;
			background-color: transparent;
		}

		body{
			background-color: #f3f3f4;
		}

		hr{
			opacity: 0.4;
		}
	</style>

</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-lg-10 offset-lg-1 text-center">	
				<div class="well">
					<h1>Gallery</h1> <a href="/upload.php" class="btn btn-primary btn-md">Upload a picture</a> <hr/>

					<div class="gallery row">
						<div class="col-lg-3 image-dummy">&nbsp;</div>
						<?php
							if (getRDShost()){
								connectToRDSInstance();

								$picures = $rdsConnection->query("SELECT `s3-raw-url`, `s3-finished-url` FROM records ORDER BY `id` DESC;");

								$colorBucket = describeS3Bucket("color");
								$bwBucket = describeS3Bucket("grayscale");

								foreach ($picures as $p) {
									$colorImg = $s3Client->getObject(["Bucket" => $colorBucket, "Key" => $p["s3-raw-url"]]);
									$colorImg = $colorImg["@metadata"]["effectiveUri"];

									$bwImg = $s3Client->getObject(["Bucket" => $bwBucket, "Key" => $p["s3-finished-url"]]);
									$bwImg = $bwImg["@metadata"]["effectiveUri"];

									echo '<a href="'.$colorImg.'" class="image" data-lightbox="'.$p["s3-raw-url"].'"><img style="width:100%" src="'.$bwImg.'"/></a>';
								}
							}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.10.0/js/lightbox.min.js"></script>
	<script type="text/javascript">
		// https://github.com/hongkhanh/gridify
		"use strict";Element.prototype.imagesLoaded=function(t){var e=this.querySelectorAll("img"),n=e.length;0==n&&t();for(var i=0,r=e.length;r>i;i++){var o=new Image;o.onload=o.onerror=function(){n--,0==n&&t()},o.src=e[i].getAttribute("src")}},Element.prototype.gridify=function(t){var e=this,t=t||{},n=function(t){for(var e=0,n=1,i=t.length;i>n;n++)t[n]<t[e]&&(e=n);return e},i=function(t,e,n){t.attachEvent?t.attachEvent("on"+e,n):t.addEventListener&&t.addEventListener(e,n)},r=function(t,e,n){t.detachEvent?t.detachEvent("on"+e,n):t.removeEventListener&&t.removeEventListener(e,o)},o=function(){e.style.position="relative";var i=e.querySelectorAll(t.srcNode),r=(t.transition||"all 0.5s ease")+", height 0, width 0",o=e.clientWidth,a=parseInt(t.margin||0),s=parseInt(t.max_width||t.width||220),l=Math.max(Math.floor(o/(s+a)),1),h=1==l?a/2:o%(s+a)/2,d=[];t.max_width&&(l=Math.ceil(o/(s+a)),s=(o-l*a-a)/l,h=a/2);for(var c=0;l>c;c++)d.push(0);for(var c=0,v=i.length;v>c;c++){var u=n(d);i[c].setAttribute("style","width: "+s+"px; position: absolute; margin: "+a/2+"px; top: "+(d[u]+a/2)+"px; left: "+((s+a)*u+h)+"px; transition: "+r),d[u]+=i[c].clientHeight+a}};this.imagesLoaded(o),t.resizable&&(i(window,"resize",o),i(e,"DOMNodeRemoved",function(){r(window,"resize",o)}))};

		$(document).ready(function(){
		      var options =
		      {
		           srcNode: '.image',             // grid items (class, node)
		           margin: '10px',             // margin in pixel, default: 0px
		           width: $(".image-dummy").width()+"px",             // grid item width in pixel, default: 220px
		           max_width: '',              // dynamic gird item width if specified, (pixel)
		           resizable: true,            // re-layout if window resize
		           transition: 'all 0.5s ease' // support transition for CSS3, default: all 0.5s ease
		      }
		      document.querySelector('.gallery').gridify(options);
		});
	</script>
</body>
</html>