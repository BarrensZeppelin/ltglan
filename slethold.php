<?php
	$allowIncludes = true;
	require "login/includes.php";

	if(!verify_login()) {header("Location: ./");}
	
	
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
	if($row['leaderID'] == $bruger['id']) {
		mysql_query("DELETE FROM teams WHERE id='" . $id . "'");
		mysql_query("DELETE FROM deltagere WHERE teamID=". $id);
		mysql_query("DELETE FROM invites WHERE team_id='" . $id . "' AND tournament_id='" . $tid . "'");
		mysql_query("DELETE FROM beskeder WHERE indhold LIKE '%" . $row["navn"] . "%" . $turnering_navn . "%'");

		echo mysql_error();
		
		
		header("Location: ./");
		die("<br/>Dit hold er blevet slettet. Du omdirigeres til hovedsiden om 3 sekunder.");
	}
	
	
	
?>