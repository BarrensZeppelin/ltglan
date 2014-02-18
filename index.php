<?php
	require "login/includes.php";
	
	// Siden er ikke funktionel i Internet Explorer og Opera, derfor beder jeg brugeren om at hente en af de to browsere til at navigere siden med i stedet
	if(browser_is_valid()) {
		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />Siden understøtter desværre hverken Internet Explorer eller Opera.<br/><a href='https://www.google.dk/?gws_rd=cr&ei=7p3wUs2cB4K8ygOAhoDwDg#newwindow=1&q=%22Mozilla+Firefox%22+OR+%22Google+Chrome%22&safe=off'>Bedre Browsere</a>
		<br/><a href='http://affiliates.mozilla.org/link/banner/52591'><img src='http://affiliates.mozilla.org/media/uploads/banners/7d34913edaae15f63174475463361ed2df8b0c3f.png' /	></a>";
		die();
	}
	
	// Hvis man prøver at logge ud, så opret en ny session og start forfra
	if(isset($_GET['logoff']) || isset($_GET['logout'])) {session_destroy(); header("Location: ./");}

	
	$loginfailed = false;
	if(isset($_POST["billetnr"], $_POST["pass"])) { //Brugeren prøver at logge ind - så kør login funktionen
		if(!empty($_POST["billetnr"]) && !empty($_POST["pass"])) {
			if(!login()) {$loginfailed = true;}
		}
	}

	include "home.php";
?>