<?php
	if(!isset($_GET["hash"])) {
		header("Location: ./");
		exit;
	}

	$allowIncludes = true;
	require "login/includes.php";

	if(!verify_login()) {header("Location: ./");exit;}
	
	
	$hash = mysql_real_escape_string($_GET["hash"]);
	
	
	
	
	if(mysql_num_rows(mysql_query("SELECT * FROM invites WHERE hash='$hash'"))==0) {
		header("refresh: 2; ./");
		die("Denne invitation kunne ikke findes i databasen. Du bliver sendt tilbage til hovedsiden.");
	}
	
	$billetnr = intval($_SESSION['billetnr']);
	$userid = mysql_result(mysql_query("SELECT id FROM guests WHERE billetnr=$billetnr"), 0);
	
	
	$team_id = mysql_result(mysql_query("SELECT team_id FROM invites WHERE hash='$hash'"),0);
	$tournament_id = mysql_result(mysql_query("SELECT tournament_id FROM teams WHERE id=$team_id"), 0);;
	
	
	$max_spillere = mysql_result(mysql_query("SELECT max_spillere FROM tournaments WHERE id=$tournament_id"), 0);

	$team_navn = mysql_result(mysql_query("SELECT navn FROM teams WHERE id=$team_id"), 0);
	
	//hvis spilleren allerede er i et hold, die
	$query = mysql_query("SELECT * FROM deltagere WHERE guest_id=$userid");
	while( $deltager = mysql_fetch_array($query) ) {
		$team = get_team($deltager['team_id']);
		if($team['tournament_id'] == $tournament_id) {
			header("refresh: 2; ./");
			die("Du er allerede en del af et hold, og kan derfor ikke acceptere invationer.");
		}
	}
	
	
	//Tjek så om holdet er fyldt op
	if(mysql_num_rows(mysql_query("SELECT * FROM deltagere WHERE team_id=$team_id"))==$max_spillere) {
		header("refresh: 2; ./");
		die("Dette hold er allerede fyldt op.");
	}
	
	
	//Find pladsen hvor brugeren skal skrives ind
	$positionquery = mysql_query("SELECT MAX(pos) FROM deltagere WHERE team_id=$team_id");
	$pos = mysql_result($positionquery,0)+1;
	
	//Tilføj den nye spiller til holdet
	mysql_query("INSERT INTO deltagere (guest_id, team_id, pos)
				VALUES ($userid, $team_id, $pos)") or die(mysql_error());
	
	if(($pos+1) == $max_spillere) {
		mysql_query("UPDATE teams SET teamstatus='Accepted'
					WHERE id=$team_id");
					
		// Opdater challonge
		$link = mysql_result(mysql_query("SELECT bracketlink FROM tournaments WHERE id=$tournament_id"), 0);
		if($link != "") {
			$c = connect_challonge();
			
			$ct = $c->makeCall("tournaments/" . $link, array("include_matches" => 0), "get");
			
			$params = array(
				"participant[name]" => $team_navn,
			);
			
			$c->createParticipant($ct->id, $params);
			
			$leader = mysql_result(mysql_query("SELECT leader_id FROM teams WHERE id=$team_id"), 0);
			send_message($leader, "Dit hold er nu fyldt og tilføjet til brackets.", -1);
		}
	}
	
	header("Location: ./");
	die("Du er nu medlem af holdet $team_navn");
?>