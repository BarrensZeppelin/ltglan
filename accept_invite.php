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
	$userid = mysql_result(mysql_query("SELECT id FROM guests WHERE billetnr=$billetnr"), 0);
	
	
	$query = mysql_query("SELECT * FROM invites WHERE hash='$hash'");
	$row = mysql_fetch_array($query);
	$team_id = $row["team_id"];
	$tournament_id = mysql_result(mysql_query("SELECT tournament_id FROM teams WHERE id=$team_id"), 0);;
	
	
	$max_spillere = mysql_result(mysql_query("SELECT max_spillere FROM tournaments WHERE id=" . $tournament_id), 0);

	$team_navn = mysql_result(mysql_query("SELECT navn FROM teams WHERE id=$team_id"), 0);
	
	//hvis spilleren allerede er i et hold, die - TEST!!!
	$query = mysql_query("SELECT * FROM deltagere WHERE guest_id=$userid");
	while( $deltager = mysql_fetch_array($query) ) {
		$team = get_team($deltager['team_id']);
		if($team['tournament_id'] == $tournament_id) {
			header("refresh: 3; ./");
			die("Du er allerede en del af et hold, og kan derfor ikke acceptere invationer.");
		}
	}
	
	
	//Tjek sa om holdet er fyldt op(?) - TEST!!!
	if(mysql_num_rows(mysql_query("SELECT * FROM deltagere WHERE team_id=".$team_id))==$max_spillere) {
		header("refresh: 3; ./");
		die("Dette hold er allerede fyldt op.");
	}
	
	
	//Find pladsen hvor brugeren skal skrives ind
	$positionquery = mysql_query("SELECT MAX(pos) FROM deltagere WHERE team_id=$team_id");
	$pos = mysql_result($positionquery,0)+1;
	
	//Tilfoj den nye spiller til holdet
	mysql_query("INSERT INTO deltagere (guest_id, team_id, pos)
				VALUES ($userid, $team_id, $pos)") or die(mysql_error());
	
	if(($pos+1) == $max_spillere) {
		mysql_query("UPDATE teams SET teamstatus='Accepted'
					WHERE id='". $team_id . "'");
	}
	
	header("refresh: 3; ./");
	die("Du er nu medlem af holdet " . $team_navn);
?>