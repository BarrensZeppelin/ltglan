<?php
	$allowIncludes = true;
	require "login/includes.php";

	if(!verify_login()) {header("refresh 2; ./");die("Du er ikke logget ind.");}
	
	
	if(!isset($_GET["tid"], $_GET["id"])) {
		header("Location: ./");
		exit;
	}
	
	$tid = intval($_GET["tid"]);
	$id = intval($_GET["id"]);
	
	$turnering_navn = mysql_result(mysql_query("SELECT navn FROM tournaments WHERE id=$tid"), 0);
	
	$team = get_team(id);
	
	
	$bruger = mysql_fetch_array(mysql_query("SELECT * FROM guests WHERE billetnr=". $_SESSION['billetnr']));
	if($team['leader_id'] == $bruger['id']) {
		slet_hold($id);
		
		header("refresh: 2; ./");
		die("Dit hold er blevet slettet. Du omdirigeres til hovedsiden om 2 sekunder.");
	}
	
?>