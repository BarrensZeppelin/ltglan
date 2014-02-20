<?php
	
	if(isset($_POST["billetnr"], $_POST["navn"], $_POST["pass"], $_POST["klasse"])) {
		//alt info er indsat.
		//tjek om info er ok
		$allowIncludes = true;
		require 'login/includes.php';
		
		$klassearray = get_klasse_array();
		
		$billet_nr = intval($_POST["billetnr"]);
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
		
		if(mysql_num_rows(mysql_query("SELECT * FROM guests WHERE billetnr=$billet_nr"))!=0) {
			header("refresh: 2; ./");
			die("Dette billetnummer er allerede blevet registreret.");
		}
		
		// FOR AT GØRE DET NEMMERE AT TESTE SIDEN
		/*if(mysql_num_rows(mysql_query("SELECT * FROM billetnr WHERE billetnr=$billet_nr"))==0) {
			header("refresh: 2; ./");
			die("Dette billetnummer kunne ikke findes i databasen.");
		}*/
		
		
		
		//alle kriterier er opfyldte
		mysql_query("INSERT INTO guests (pass_hashed, billetnr, navn, klasse)
					 VALUES ('$pass_hashed', '$billet_nr', '$navn', '$klasse')") or die(mysql_error());
		
		
		// Send velkomstbesked
		$gid = get_guest_wbilletnr($billet_nr);
		$gid = $gid['id'];
		send_message($gid, "Velkommen til LTGLANs turneringsside! Her finder du alle de aktive turneringer, dine hold og mulighed for tilmelding, klik blot på din favoritturninger for at komme i gang. ;)", -1);
		send_message($gid, "I dette felt finder du invitationer til turneringer fra andre spillere, opdateringer og generelle beskeder mm.<br/>Du kan slette en besked ved at trykke på krydset i øverste højre hjørne.", -1);
		
		
		login($billet_nr, $pass_hashed);
		
		header("Location: ./");
		echo "Brugeren er registret. Siden omdirigerer dig automatisk.";
	}
	
	
?>