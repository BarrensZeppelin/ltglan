<?php
	$allowIncludes = true;
	require "login/includes.php";

	if(!verify_login()) {header("refresh 2; ./");die("Du er ikke logget ind.");}
	
	
	if(!isset($_GET["tid"], $_GET["id"])) {
		header("Location: ./");
		exit;
	}
	
	$tid = $_GET["tid"];
	$id = $_GET["id"];
	
	$tid = mysql_real_escape_string($tid);
	$id = mysql_real_escape_string($id);
	
	
	
	$arr = mysql_fetch_array(mysql_query("SELECT * FROM tournaments WHERE id=$tid"));
	
	$turnering_navn = $arr["navn"];
	
	$row = mysql_fetch_array(mysql_query("SELECT * FROM teams WHERE id=$id"));
	
	
	$bruger = mysql_fetch_array(mysql_query("SELECT * FROM guests WHERE billetnr=". $_SESSION['billetnr']));
	if($row['leader_id'] == $bruger['id']) {
		if($row['avatarpath'] != null && $row['avatarpath'] != "") unlink($row['avatarpath']); // Slet avatar
	
		mysql_query("DELETE FROM teams WHERE id='" . $id . "'") or die(mysql_error());
		mysql_query("DELETE FROM deltagere WHERE team_id=". $id) or die(mysql_error());
		mysql_query("DELETE FROM invites WHERE team_id='" . $id . "' AND tournament_id='" . $tid . "'") or die(mysql_error());
		mysql_query("DELETE FROM beskeder WHERE indhold LIKE '%" . $row["navn"] . "%" . $turnering_navn . "%'") or die(mysql_error());
		
		
		header("refresh: 2; ./");
		die("Dit hold er blevet slettet. Du omdirigeres til hovedsiden om 2 sekunder.");
	}
	
	
	
?>