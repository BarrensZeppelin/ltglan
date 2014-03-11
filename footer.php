<div id="footer">
	 <div id="footer-box1">
		 <p id="alle-rettigheder"><a class="dogelink" href="./?cmms=1">&copy;</a> 2014 LAN-gruppen på LTG<br />
		Alle rettigheder forbeholdes</p>
		</div>
		
	 <div id="footer-box2">
		<?php if(isset($_SESSION["admin"]) && $_SESSION["admin"]) {
				echo "<p align='center'><a class='dogelink' target='blank' href='./admin.php'>Vær Admin :D</a></p>";
			} 
			
			/*if(isset($_SESSION['cmms']) && $_SESSION['cmms']) {
				?> <iframe style="display:inline-block;border: 0; width: 300px; height: 42px;" src="http://bandcamp.com/EmbeddedPlayer/album=61306845/size=small/bgcol=333333/linkcol=ffffff/transparent=true/" seamless><a href="http://atismuskk.bandcamp.com/album/den-gyldne-br-nd">Den Gyldne Brønd by AtisMuskk</a></iframe> <?php 
			} else if*/
			if(!isset($_SESSION["admin"]) || $_SESSION["admin"] == false) {
				echo '
				<p align="center" id="footer-kontakt">
					HTX LAN LTG
				</p>
					
				<p align="center" id="footer-kontakt">
				 LTG LAN-Gruppen | Akademivej 451
				</p>';
			}
		?>
	</div>
		
	 <div id="footer-box3">
		<?php if(isset($_SESSION['cmms']) && $_SESSION['cmms']) { ?>
		<div class="facebook">
			<a target="_blank" href="http://autistklassen.dk/"><img align="right" src="imgs/autistklassen.png" width="48" height="48" alt="autistklassen" /></a>
		</div>
		<?php } ?>
	 
		<div class="facebook">
			<a target="_blank" href="https://www.facebook.com/events/632966440097615/"><img align="right" src="imgs/facebook_48.png" width="48" height="48" alt="facebook" /></a>
		</div>
	</div>
</div>
