<?php
	require "login/includes.php";
	
	if(browser_is_valid()) {
		die("Siden underst�tter desv�rre hverken Internet Explorer eller Opera.");
	}
	
	if(isset($_GET['logoff']) || isset($_GET['logout'])) {session_destroy(); header("Location: ./");}

	$loginfailed = false;
	if(isset($_POST["billetnr"]) && isset($_POST["pass"])) { //Brugeren prøver at logge ind - så kør login funktionen
		if(!empty($_POST["billetnr"]) && !empty($_POST["pass"])) {
			if(!login()) {$loginfailed = true;}
		}
	}

	include "home.php";
?>