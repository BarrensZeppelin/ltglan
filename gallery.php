<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js" type="text/javascript"></script>
<!--script src="js/jquery.lint.js" type="text/javascript" charset="utf-8"></script-->
<link rel="stylesheet" href="./css/prettyPhoto.css" type="text/css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
<script src="scripts/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		$("area[rel^='prettyPhoto']").prettyPhoto();
		
		$(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'normal',theme:'facebook',slideshow:4000, autoplay_slideshow: true});
		$(".gallery:gt(0) a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'fast',theme:'facebook',slideshow:4000, hideflash: true});

		$("#custom_content a[rel^='prettyPhoto']:first").prettyPhoto({
			custom_markup: '<div id="map_canvas" style="width:260px; height:265px"></div>',
			changepicturecallback: function(){ initialize(); }
		});

		$("#custom_content a[rel^='prettyPhoto']:last").prettyPhoto({
			custom_markup: '<div id="bsap_1259344" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6"></div><div id="bsap_1237859" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6" style="height:260px"></div><div id="bsap_1251710" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6"></div>',
			changepicturecallback: function(){ _bsap.exec(); }
		});
	});
</script>

<?php
	if(!isset($_GET['page'])) {
		header("Location: ./?p=gallery&page=1");
	}
	
	$page = $_GET['page'];
	if($page>4 or $page<1) {header("Location: ./?p=gallery&page=1");}
	$page-=1;
?>

				<div class="wrapper-galleri-bokse" style="padding-top:30px;">
                	<div class="wrapper-bokse-margin" id="no-margin">
                        <div class="wrapper-bokse" id="no-margin">
							<div class="gallery clearfix">
            					<a href="./imgs/galleri/fullscreen/<?php echo 1+$page*12; ?>.jpg" rel="prettyPhoto[pp_gal]" title="You can add caption to pictures."><img src="./imgs/galleri/thumbmails/<?php echo 1+$page*12; ?>.jpg" width="150" height="150" alt="" /></a>
    						</div>
                        </div>
                        <div class="wrapper-bokse">
							<div class="gallery clearfix">
            					<a href="./imgs/galleri/fullscreen/<?php echo 2+$page*12; ?>.jpg" rel="prettyPhoto[pp_gal]" title="You can add caption to pictures."><img src="./imgs/galleri/thumbmails/<?php echo 2+$page*12; ?>.jpg" width="150" height="150" alt="" /></a>
    						</div>
                        </div>
                        <div class="wrapper-bokse">
							<div class="gallery clearfix">
            					<a href="./imgs/galleri/fullscreen/<?php echo 3+$page*12; ?>.jpg" rel="prettyPhoto[pp_gal]" title="You can add caption to pictures."><img src="./imgs/galleri/thumbmails/<?php echo 3+$page*12; ?>.jpg" width="150" height="150" alt="" /></a>
    						</div>
                        </div>
                        <div class="wrapper-bokse">
							<div class="gallery clearfix">
            					<a href="./imgs/galleri/fullscreen/<?php echo 4+$page*12; ?>.jpg" rel="prettyPhoto[pp_gal]" title="You can add caption to pictures."><img src="./imgs/galleri/thumbmails/<?php echo 4+$page*12; ?>.jpg" width="150" height="150" alt="" /></a>
    						</div>
                        </div>
                    </div>
                </div>
                
                <div class="wrapper-galleri-bokse">
                	<div class="wrapper-bokse-margin">
                        <div class="wrapper-bokse" id="no-margin">
							<div class="gallery clearfix">
            					<a href="./imgs/galleri/fullscreen/<?php echo 5+$page*12; ?>.jpg" rel="prettyPhoto[pp_gal]" title="You can add caption to pictures."><img src="./imgs/galleri/thumbmails/<?php echo 5+$page*12; ?>.jpg" width="150" height="150" alt="" /></a>
    						</div>
                        </div>
                        <div class="wrapper-bokse">
							<div class="gallery clearfix">
            					<a href="./imgs/galleri/fullscreen/<?php echo 6+$page*12; ?>.jpg" rel="prettyPhoto[pp_gal]" title="You can add caption to pictures."><img src="./imgs/galleri/thumbmails/<?php echo 6+$page*12; ?>.jpg" width="150" height="150" alt="" /></a>
    						</div>
                        </div>
                        <div class="wrapper-bokse">
							<div class="gallery clearfix">
            					<a href="./imgs/galleri/fullscreen/<?php echo 7+$page*12; ?>.jpg" rel="prettyPhoto[pp_gal]" title="You can add caption to pictures."><img src="./imgs/galleri/thumbmails/<?php echo 7+$page*12; ?>.jpg" width="150" height="150" alt="" /></a>
    						</div>
                        </div>
                        <div class="wrapper-bokse">
							<div class="gallery clearfix">
            					<a href="./imgs/galleri/fullscreen/<?php echo 8+$page*12; ?>.jpg" rel="prettyPhoto[pp_gal]" title="You can add caption to pictures."><img src="./imgs/galleri/thumbmails/<?php echo 8+$page*12; ?>.jpg" width="150" height="150" alt="" /></a>
    						</div>
                        </div>
                    </div>
                </div>
                
                <div class="wrapper-galleri-bokse1">
                	<div class="wrapper-bokse-margin" id="no-margin">
                        <div class="wrapper-bokse" id="no-margin">
							<div class="gallery clearfix">
            					<a href="./imgs/galleri/fullscreen/<?php echo 9+$page*12; ?>.jpg" rel="prettyPhoto[pp_gal]" title="You can add caption to pictures."><img src="./imgs/galleri/thumbmails/<?php echo 9+$page*12; ?>.jpg" width="150" height="150" alt="" /></a>
    						</div>
                        </div>
                        <div class="wrapper-bokse">
							<div class="gallery clearfix">
            					<a href="./imgs/galleri/fullscreen/<?php echo 10+$page*12; ?>.jpg" rel="prettyPhoto[pp_gal]" title="You can add caption to pictures."><img src="./imgs/galleri/thumbmails/<?php echo 10+$page*12; ?>.jpg" width="150" height="150" alt="" /></a>
    						</div>
                        </div>
                        <div class="wrapper-bokse">
							<div class="gallery clearfix">
            					<a href="./imgs/galleri/fullscreen/<?php echo 11+$page*12; ?>.jpg" rel="prettyPhoto[pp_gal]" title="You can add caption to pictures."><img src="./imgs/galleri/thumbmails/<?php echo 11+$page*12; ?>.jpg" width="150" height="150" alt="" /></a>
    						</div>
                        </div>
                        <div class="wrapper-bokse">
							<div class="gallery clearfix">
            					<a href="./imgs/galleri/fullscreen/<?php echo 12+$page*12; ?>.jpg" rel="prettyPhoto[pp_gal]" title="You can add caption to pictures."><img src="./imgs/galleri/thumbmails/<?php echo 12+$page*12; ?>.jpg" width="150" height="150" alt="" /></a>
    						</div>
                        </div>
                    </div>
                </div>
                
                <div id="wrapper-billede-menu">
                	<div id="wrapper-billede-menu-margin">
                    	<div class="billede-menu">
                        <?php 
							if($page!=0) {
								echo '<a href="./?p=gallery&page='.$page.'"><img src="./imgs/galleri/menu/venstre.png" width="64" height="30" alt="Venstre-pil" /></a>';
							}
						?>
						</div>
                        <div class="billede-menu" id="pages">
                        	<p style="text-align:center"><?php echo $page+1; ?> af 4</p>
                        </div>
                        <div class="billede-menu">
						<?php 
							if($page!=3) {
								echo '<a href="./?p=gallery&page='. ($page+2) .'"><img src="./imgs/galleri/menu/hojre.png" width="64" height="30" alt="Venstre-pil" /></a>';
							}
						?>
                        </div>
                    </div>
                </div>