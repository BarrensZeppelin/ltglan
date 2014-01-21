<?php
	if(!isset($_GET["hash"])) {
		header("Location: ./");
		exit;
	}

	$allowIncludes = true;
	require "login/includes.php";

	if(!verify_login()) {header("Location: ./");}
	
	
	$hash = $_GET["hash"];
	$hash = mysql_real_escape_string($hash);
	
	
	
	
	
	if(mysql_num_rows(mysql_query("SELECT * FROM invites WHERE hash='$hash'"))==0) {
			header("refresh: 3; ./");
			die("Denne invitation kunne ikke findes i databasen. Du bliver sendt tilbage til hovedsiden.");
	}
	
	$billetnr = $_SESSION['billetnr'];
	$query = mysql_query("SELECT * FROM guests WHERE billetnr='$billetnr'");
	$row = mysql_fetch_array($query);
	$userid = $row["id"];
	
	
	$query = mysql_query("SELECT * FROM invites WHERE hash='$hash'");
	$row = mysql_fetch_array($query);
	$team_id = $row["team_id"];
	$tournament_id = $row["tournament_id"];
	
	
	$arr = mysql_fetch_array(mysql_query("SELECT * FROM tournaments WHERE id='" . $tournament_id . "'"));
	//$turnering_db_navn = $arr["db_navn"];
	$max_spillere = $arr["max_spillere"];
	

	$query = mysql_query("SELECT * FROM teams WHERE id='$team_id'");
	$row = mysql_fetch_array($query);
	$team_navn = $row["navn"];
	
	
	//hvis spilleren allerede er i et hold, die - TEST!!!
	$query = mysql_query("SELECT * FROM deltagere WHERE guest_id=".$userid." AND tournament_id=".$tournament_id);
	if(mysql_num_rows($query)>=1) {
		header("refresh: 3; ./");
		die("Du er allerede en del af et hold, og kan derfor ikke acceptere invationer.");
	}
	
	//Tjek sa om holdet er fyldt op(?) - TEST!!!
	if(mysql_num_rows(mysql_query("SELECT * FROM deltagere WHERE tournament_id=".$tournament_id." AND team_id=".$team_id))==$max_spillere) {
		header("refresh: 3; ./");
		die("Dette hold er allerede fyldt op.");
	}
	
	
	//Find pladsen hvor brugeren skal skrives ind
	$positionquery = mysql_query("SELECT MAX(pos) FROM deltagere WHERE tournament_id=$tournament_id AND team_id=$team_id");
	$pos = mysql_result($positionquery,0)+1;
	
	//Tilfoj den nye spiller til holdet
	mysql_query("INSERT INTO deltagere (guest_id, tournament_id, team_id, pos)
				VALUES ($userid, $tournament_id, $team_id, $pos)") or die(mysql_error());
	
	if(($pos+1) == $max_spillere) {
		mysql_query("UPDATE teams SET teamstatus='Accepted'
					WHERE id='". $team_id . "'");
	}
	
	header("refresh: 3; ./");
	die("Du er nu medlem af holdet " . $team_navn);
?>