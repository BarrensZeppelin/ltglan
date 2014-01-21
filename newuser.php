<?php
	$klassearray = array("1. a", "1. b", "1. c", "1. d", "1. e", "1. f", "2. a", "2. b", "2. c", "2. d", "2. e", "2. f", "3. a", "3. b", "3. c", "3. d", "3. e", "3. f", "Anden");


	
		
	
	if(isset($_POST["billetnr"], $_POST["navn"], $_POST["pass"], $_POST["klasse"])) {
		//alt info er indsat.
		//tjek om info er ok
		$allowIncludes = true;
		require 'login/connect.php';
		
		$billet_nr = mysql_real_escape_string($_POST["billetnr"]);
		$navn = mysql_real_escape_string($_POST["navn"]);
		$pass = mysql_real_escape_string($_POST["pass"]);
		$klasse = mysql_real_escape_string($_POST["klasse"]);
		
		$navn = ucwords(strtolower($navn)); //Fikser navnet, sa det forste bogstav star med stort, og resten er smat.
		
		$pass_hashed = md5($pass);
		
		
		
		// Brugeren har leget rundt med javascriptet på client-siden
		if(!in_array($klasse, $klassearray)) {
			header("refresh: 5; ./");
			die("Den klasse kunne ikke findes.");
		}
		
		if(mysql_num_rows(mysql_query("SELECT * FROM guests WHERE billetnr='$billet_nr'"))!=0) {
			header("refresh: 2; ./");
			die("Dette billetnummer er allerede blevet registreret.");
		}
		if(mysql_num_rows(mysql_query("SELECT * FROM billetnr WHERE billetnr='$billet_nr'"))==0) {
			header("refresh: 2; ./");
			die("Dette billetnummer kunne ikke findes i databasen.");
		}
		
		
		
		//alle kriterier er opfyldte
		mysql_query("INSERT INTO guests (pass_hashed, billetnr, navn, klasse)
					 VALUES ('$pass_hashed', '$billet_nr', '$navn', '$klasse')") or die(mysql_error());
		
		
		
		
		header("refresh: 2; ./");
		echo "Brugeren er registret. Siden omdirigerer dig automatisk.";
	}
	
	
?>