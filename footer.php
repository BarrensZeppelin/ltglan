<div id="footer">
	 <div id="footer-box1">
		 <p id="alle-rettigheder"><a href="./?cmms=1">&copy;</a> 2013 Bravo Computing<br />
		Alle rettigheder forbeholdes</p>
		</div>
		
	 <div id="footer-box2">
		<?php if(!isset($_SESSION["admin"]) || $_SESSION["admin"] == false) { ?>
			<p align="center" id="footer-kontakt">
				HTX LAN LTG
			</p>
				
			<p align="center" id="footer-kontakt">
			 LTG LAN-Gruppen | Akademivej 451
			</p>
		<?php } else {
			echo "<p align='center'><a target='blank' href='./admin.php'>Vær Admin :D</a></p>";
		} ?>
	</div>
		
	 <div id="footer-box3">
		<div id="facebook">
			<a target="_blank" href="https://www.facebook.com/events/632966440097615/"><img align="right" src="imgs/facebook_48.png" width="48" height="48" alt="facebook" /></a>
		</div>
	</div>
</div>
