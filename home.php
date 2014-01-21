<?php
	$klassearray = array("1. a", "1. b", "1. c", "1. d", "1. e", "1. f", "2. a", "2. b", "2. c", "2. d", "2. e", "2. f", "3. a", "3. b", "3. c", "3. d", "3. e", "3. f", "Anden");

	// TEST MOED
	//if(isset($_SESSION['billetnr'])) {if($_SESSION['billetnr'] != 000000 && $_SESSION['billetnr'] != 000001 && $_SESSION['billetnr'] != 000002) {die("Please come back later.");}}

	if(verify_login()) {
		$allowedpages = array("tournaments", "about", "gallery", "servers");
		if(!isset($_GET['p']) || !in_array($_GET['p'], $allowedpages)) { // $_GET['page'] bliver allerede brugt
			header("Location: ./?p=tournaments");
		}
	} else { // Man må godt se galleriet hvis man er logget ind
		if($_GET['p']!="front" && $_GET['p']!="gallery") { header("Location: ./?p=front"); }
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Forside | LTG LAN-PARTY</title>
		
		<link rel="stylesheet" type="text/css" href="css/generelt-style.css"/>
		<link rel="stylesheet" type="text/css" href="css/index-style.css"/>
		<link rel="stylesheet" type="text/css" href="css/turnering-style.css"/>
		<link rel="stylesheet" type="text/css" href="css/omkring-style.css"/>
		<link rel="stylesheet" type="text/css" href="css/servere-style.css"/>
		
		
		<!--	 	jQuery & jQuery UI			-->
		<link rel="stylesheet" 	href="css/ui-lightness/jquery-ui-1.10.4.custom.min.css" />
		<script 				src="scripts/jquery-2.0.3.js"></script>
		<script 				src="scripts/jquery-ui-1.10.4.js"></script>
		
		
		<!--	Egne javascript funktioner 		-->	
		<script src="scripts/functions.js"></script>
		
		<link rel="shortcut icon" href="imgs/favicon.ico" />
	</head>
	
	<body>
		<div id="wrapper">
			<?php include 'header.php'; ?>
			
			<?php include $_GET['p'] . ".php"; ?>
			
			<?php include 'footer.php'; ?>
		</div>
	</body>
</html>