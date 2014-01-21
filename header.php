<script type="text/javascript">
	var state = 0;
	var i = window.setInterval(function(){changeSponsor()}, 5000);
</script>

<div id="wrapper-logo-sponsorer">
	<div id="logo-ltg">
		<img src="imgs/LTG-logo.png" width="320" height="84" alt="LTG - logo" />
	</div>
	
	<div id="sponsor-logo">
		<!--<div id="logo-cocio">
			<a href="http://www.cocio.dk/Default.aspx" target="_blank"><img src="imgs/sponsorer/cocio1.jpg" width="140" height="47" border="1" alt="Cocio logo" /></a>
		</div>
		<div id="logo-pc-klinik">
			<a href="http://pcklinik1.dk/" target="_blank"><img src="imgs/sponsorer/pc-klinik.png" width="140" height="47" border="1" alt="PCklinik" /></a>
		</div>
		<div id="logo-bravo-internationals">
			<a href="http://mathiasbrask.dk/" target="_blank"><img src="imgs/sponsorer/bravo-internationals.png" width="187" height="47" border="1" alt="Bravo Internationals" /></a>
		</div>
		<div id="logo-shark-gaming">
			<a href="http://www.sharkgaming.dk/" target="_blank"><img src="imgs/sponsorer/sharkgaming.png" width="547" height="47" border="1" alt="SharkGaming" /></a>
		</div>-->
		<div id="logo-js">
			<a href="http://unqhosting.com/" target="_blank"><img src="imgs/sponsorer/unqhosting.png" height="80px" border="1" alt="UNQHosting" /></a>
		</div>
	</div>
</div>

<div id="wrapper-menu">
	<div class="navmenu">
		<ul>
			<li id="hjem" 		<?php if($_GET['p']=="front") echo ' class="aktiv"'; ?> 		><a href="<?php echo (verify_login() ? "./?logout" : "./"); ?>"><img src="imgs/menu/home.png" width="32" height="32" alt="hjem" /><span><?php echo (verify_login() ? "Log Ud" : "Forside"); ?></span></a></li>
			<li id="turneringer"<?php if($_GET['p']=="tournaments") echo ' class="aktiv"'; ?> 	><a href="./?p=tournaments"><img src="imgs/menu/turneringer.png" width="32" height="32" alt="turneringer" />Turneringer</a></li>
			<li id="galleri" 	<?php if($_GET['p']=="gallery") echo ' class="aktiv"'; ?> 		><a href="./?p=gallery&page=1"><img src="imgs/menu/galleri.png" width="32" height="32" alt="galleri" />Galleri</a></li>
			<li id="servere" 	<?php if($_GET['p']=="servers") echo ' class="aktiv"'; ?> 		><a href="./?p=servers"><img src="imgs/menu/servere.png" width="32" height="32" alt="omkring" />Servere</a></li>
			<li id="omkring" 	<?php if($_GET['p']=="about") echo ' class="aktiv"'; ?> 		><a href="./?p=about"><img src="imgs/menu/omkring.png" width="32" height="32" alt="omkring" />Omkring</a></li>
			<!--<li id="kontakt" 	<?php //if($_GET['p']=="contact") echo ' class="aktiv"'; ?> 		><a href="kontakt.html"><img src="imgs/menu/kontakt.png" width="32" height="32" alt="log ud" />Kontakt</a></li>-->
		</ul>
	</div>
</div>