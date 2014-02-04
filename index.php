<?php
	require "login/includes.php";
	
	if(browser_is_valid()) {
		die("Siden understøtter desværre hverken Internet Explorer eller Opera.<br/><a href='https://www.google.dk/?gws_rd=cr&ei=7p3wUs2cB4K8ygOAhoDwDg#newwindow=1&q=%22Mozilla+Firefox%22+OR+%22Google+Chrome%22&safe=off'>Bedre Browsere</a>");
	}
	
	if(isset($_GET['logoff']) || isset($_GET['logout'])) {session_destroy(); header("Location: ./");}

	$loginfailed = false;
	if(isset($_POST["billetnr"]) && isset($_POST["pass"])) { //Brugeren prÃ¸ver at logge ind - sÃ¥ kÃ¸r login funktionen
		if(!empty($_POST["billetnr"]) && !empty($_POST["pass"])) {
			if(!login()) {$loginfailed = true;}
		}
	}

	include "home.php";
?>