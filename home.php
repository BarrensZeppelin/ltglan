<?php
	$klassearray = get_klasse_array();

	// TEST MOED
	//if(isset($_SESSION['billetnr'])) {if($_SESSION['billetnr'] != 000000 && $_SESSION['billetnr'] != 000001 && $_SESSION['billetnr'] != 000002) {die("Please come back later.");}}

	if(isset($_GET['cmms'])) {
		if(!isset($_SESSION['cmms'])) {
			$_SESSION['cmms'] = true;
		} else {
			if($_SESSION['cmms']) $_SESSION['cmms'] = false;
			else $_SESSION['cmms'] = true;
		}
		
		header("Location: ./");
	}
	
	if(verify_login()) {
		$allowedpages = array("front", "tournaments", "about", "gallery", "servers");
		if(!isset($_GET['p']) || !in_array($_GET['p'], $allowedpages)) { // $_GET['page'] bliver allerede brugt
			header("Location: ./?p=front");
			exit;
		}
	} else { // Man må godt se frontsiden, galleriet og omkring hvis man ikke er logget ind
		if(!isset($_GET['p']) || ($_GET['p']!="front" && $_GET['p']!="gallery" && $_GET['p']!="about")) { 
			$w = "";
			if(isset($_GET['p']) && ($_GET['p'] == "tournaments" || $_GET['p'] == "servers")) {
				$w = "&w=" . $_GET['p'];
			}
			
			header("Location: ./?p=front" . $w); 
			exit;
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Forside | LTG LAN-PARTY</title>
		
		<!--			Stylesheets				-->
		<link rel="stylesheet" type="text/css" href="css/generelt-style.css"/>
		<link rel="stylesheet" type="text/css" href="css/index-style.css"/>
		<link rel="stylesheet" type="text/css" href="css/turnering-style.css"/>
		<link rel="stylesheet" type="text/css" href="css/omkring-style.css"/>
		<link rel="stylesheet" type="text/css" href="css/servere-style.css"/>
		
		
		<!--	 	jQuery & jQuery UI			-->
		<link rel="stylesheet" 	href="css/ui-lightness/jquery-ui-1.10.4.custom.css" />
		<script 				src="scripts/jquery-2.0.3.js"></script>
		<script 				src="scripts/jquery-ui-1.10.4.js"></script>
		
		
		<!--	Egne javascript funktioner 		-->	
		<script src="scripts/functions.js"></script>
		
		<?php
			if($loginfailed != 0 || isset($_GET['w']) || isset($_POST['alert'])) { ?>
				<script type="text/javascript">
					$(document).ready(function () {
						<?php if($loginfailed != 0) { ?>
							alert("<?php if($loginfailed == 1) echo "En bruger med dette billetnr eksisterer ikke."; else echo "Passwordet passer ikke til denne bruger."; ?>");
						<?php } else if(isset($_GET['w'])) { ?>
							alert("Opret en bruger og log ind først. :)");
						<?php } else {
							echo "alert('". htmlspecialchars($_POST['alert']) ."');";
						} ?>
					});
				</script>
		<?php } ?>
		
		
		<?php
			// Denne kode gør alle fonts på hjemmesiden til Comic Sans MS (For sjov)
			if(isset($_SESSION['cmms'])) {
				if($_SESSION['cmms']) {
					echo "<style>
						body div{ 
							font-family: \"Comic Sans MS\" !important;
						}
						
						body div a{ 
							font-family: \"Comic Sans MS\" !important;
						}
						
						body div p{ 
							font-family: \"Comic Sans MS\" !important;
						}
						</style>";
				}
			}
		?>
		
		<link rel="shortcut icon" href="imgs/favicon.ico" />
	</head>
	
	<body>
		<div id="wrapper">
			<!-- Inkludér Header, footer og den valgte side -->
			<?php include 'header.php'; ?>
			
			<?php include $_GET['p'] . ".php"; ?>
			
			<?php include 'footer.php'; ?>
		</div>
	</body>
</html>