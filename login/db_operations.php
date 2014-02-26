<?php 
/* Denne fil er til for at lette koden, og for at slippe for at skrive kode der gentager sig selv om og om igen. */


function send_message($modtager, $indhold, $afsender = 0) {
	if($afsender != -1) {
		$afsender = get_guest_wbilletnr(intval($_SESSION['billetnr']));
		$afsender = $afsender['id'];
	}
	
	mysql_query("INSERT INTO beskeder (modtager_id, afsender_id, indhold)
				 VALUES ($modtager, $afsender, '$indhold')") or die(mysql_error());
}


// Simple ID Fetches

function get_deltager($id) {
	$deltagerquery = mysql_query("SELECT * FROM deltagere WHERE id=$id") or die(mysql_error());
	
	return mysql_fetch_array($deltagerquery);
}

function get_guest($id) {
	$guestquery = mysql_query("SELECT * FROM guests WHERE id=$id");
	
	return mysql_fetch_array($guestquery);
}

function get_guest_wbilletnr($billetnr) {
	$guestquery = mysql_query("SELECT * FROM guests WHERE billetnr=$billetnr");
	
	return mysql_fetch_array($guestquery);
}


function get_team($id) {
	$teamquery = mysql_query("SELECT * FROM teams WHERE id=$id");
	
	return mysql_fetch_array($teamquery);
}

function get_tournament($id) {
	$tquery = mysql_query("SELECT * FROM tournaments WHERE id=$id");
	
	return mysql_fetch_array($tquery);
}

////////////////////////////////




function slet_hold($holdid) {
	$team = get_team($holdid);

	// Delete challonge participant (Sker kun hvis holdet findes på challonge) //
	$t = get_tournament($team['tournament_id']);
	
	if(isset($t['bracketlink']) && $t['bracketlink'] != "" && $team['teamstatus'] == "Accepted") {
		
		$c = connect_challonge();
		
		$ct = $c->makeCall("tournaments/". $t['bracketlink'], array(), "get");
		$participants = $c->makeCall("tournaments/". $t['bracketlink'] ."/participants");
		
		for($i = 0; $i < $ct->{'participants-count'}; $i++) {
			if($participants->participant[$i]->name == $team['navn']) {
				$c->makeCall("tournaments/". $t['bracketlink'] ."/participants/". $participants->participant[$i]->id, array(), "delete");
				break;
			}
		}
	}
	///////////////////////////////////
	
	
	
	$turnering_navn = mysql_result(mysql_query("SELECT navn FROM tournaments WHERE id=". $team['tournament_id']), 0);
	
	if($team['avatarpath'] != null && $team['avatarpath'] != "") unlink($team['avatarpath']); // Slet avatar

	// Slet alt der har med holdet at gøre
	mysql_query("DELETE FROM deltagere WHERE team_id=". $team['id']) or die(mysql_error());
	mysql_query("DELETE FROM invites WHERE team_id=" . $team['id']) or die(mysql_error());
	mysql_query("DELETE FROM teams WHERE id=" . $team['id']) or die(mysql_error());
	mysql_query("DELETE FROM beskeder WHERE indhold LIKE '%" . $team["navn"] . "%" . $turnering_navn . "%'") or die(mysql_error());
}


function slet_deltager($deltagerid) {
	$deltager = get_deltager($deltagerid);
	
	$team = get_team($deltager['team_id']);
	if($deltager['pos'] == 0) {	
		
		// Hvis denne deltager er leder af et hold, kan vi bare slette hele holdet.
		slet_hold($team['id']);
		
	} else {
		// Ellers så slet den ene deltager og flyt rundt påe andre, så den sidste i holdet tager den slettedes plads
	
		// Delete challonge participant //
		$t = get_tournament($team['tournament_id']);
		
		if(isset($t['bracketlink']) && $t['bracketlink'] != "" && $team['teamstatus'] == "Accepted") {
			
			$c = connect_challonge();
			
			$ct = $c->makeCall("tournaments/". $t['bracketlink'], array(), "get");
			$participants = $c->makeCall("tournaments/". $t['bracketlink'] ."/participants");
			
			for($i = 0; $i < $ct->{'participants-count'}; $i++) {
				if($participants->participant[$i]->name == $team['navn']) {
					$c->makeCall("tournaments/". $t['bracketlink'] ."/participants/". $participants->participant[$i]->id, array(), "delete");
					
					send_message($team['leader_id'], "Dit hold: <i>". $team['navn'] ."</i> er midlertidigt fjernet fra brackets, da det ikke længere er fyldt.<br/>Skynd dig at få de sidste spiller med inden turneringen starter!", -1);
					break;
				}
			}
		}
		///////////////////////////////////
	
		$turnering_navn = mysql_result(mysql_query("SELECT navn FROM tournaments WHERE id=". $team['tournament_id']), 0);
		
		// Slet deltageren, opdater holdet, slet beskeder
		mysql_query("DELETE FROM deltagere WHERE id=". $deltager['id']);
		mysql_query("UPDATE teams SET teamstatus='Pending' WHERE id=". $team['id']) or die(mysql_error());
		mysql_query("DELETE FROM beskeder WHERE indhold LIKE '%" . $team["navn"] . "%" . $turnering_navn . "%' AND modtager_id=". $deltager['guest_id']) or die(mysql_error());
		
		
		/*
			Koden herunder flytter spillere rundt
		
			1. Leder
			2. Del 1
			3. Del 2 <- skal slettes
			4. Del 3
			
				|
				|
				v
			
			1. Leder
			2. Del 1
			3. Del 3
		*/
		
		$pos = $deltager['pos'];
		
		if($pos != (mysql_result(mysql_query("SELECT MAX(pos) FROM deltagere WHERE team_id=". $team['id']), 0)+1)) {
			$deltager2 = mysql_fetch_array(mysql_query("SELECT * FROM deltagere WHERE team_id=". $team['id'] ." ORDER BY pos DESC LIMIT 1"));
			mysql_query("UPDATE deltagere SET pos=$pos WHERE id=". $deltager2['id']);
		}
	}
}

function ny_deltager($guest_id, $team_id, $hash = "") {
	$tournament_id = mysql_result(mysql_query("SELECT tournament_id FROM teams WHERE id=$team_id"), 0);
	
	//hvis spilleren allerede er i et hold, die
	$query = mysql_query("SELECT * FROM deltagere WHERE guest_id=$guest_id");
	while( $deltager = mysql_fetch_array($query) ) {
		$team = get_team($deltager['team_id']);
		if($team['tournament_id'] == $tournament_id) {
			post_to("./?p=tournaments", array("alert" => "Du er allerede en del af et hold, og kan derfor ikke acceptere invationer."));
		}
	}
	
	
	//Tjek så om holdet er fyldt op
	$max_spillere = mysql_result(mysql_query("SELECT max_spillere FROM tournaments WHERE id=$tournament_id"), 0);
	
	if(mysql_num_rows(mysql_query("SELECT * FROM deltagere WHERE team_id=$team_id")) == $max_spillere) {
		post_to("./?p=tournaments", array("alert" >= "Dette hold er allerede fyldt op."));
	}
	
	
	//Find pladsen hvor brugeren skal skrives ind
	$positionquery = mysql_query("SELECT MAX(pos) FROM deltagere WHERE team_id=$team_id");
	$pos = mysql_result($positionquery,0)+1;
	
	//Tilføj den nye spiller til holdet
	mysql_query("INSERT INTO deltagere (guest_id, team_id, pos)
				VALUES ($guest_id, $team_id, $pos)") or die(mysql_error());
	
	// Slet invite og besked(er) til spilleren
	$turnering_navn = mysql_result(mysql_query("SELECT navn FROM tournaments WHERE id=$tournament_id"), 0);
	$leaderid = mysql_result(mysql_query("SELECT leader_id FROM teams WHERE id=$team_id"), 0);
	
	if($hash != "") mysql_query("DELETE FROM invites WHERE hash='$hash' LIMIT 1") or die(mysql_query());
	mysql_query("DELETE FROM beskeder WHERE indhold LIKE '%$turnering_navn%' AND modtager_id=$guest_id AND afsender_id=$leaderid") or die(mysql_error());
	
	if(($pos+1) == $max_spillere) {
		mysql_query("UPDATE teams SET teamstatus='Accepted'
					WHERE id=$team_id");
					
					
		$team_navn = mysql_result(mysql_query("SELECT navn FROM teams WHERE id=$team_id"), 0);
		$seed = mysql_result(mysql_query("SELECT seed FROM teams WHERE id=$team_id"), 0);
		
		
		// Opdater challonge
		$link = mysql_result(mysql_query("SELECT bracketlink FROM tournaments WHERE id=$tournament_id"), 0);
		if($link != "") {
			$c = connect_challonge();
			
			$ct = $c->makeCall("tournaments/" . $link, array("include_matches" => 0), "get");
			
			$seedpos = mysql_num_rows(mysql_query("SELECT * FROM teams WHERE tournament_id=$tournament_id AND teamstatus='Accepted' AND seed < $seed")) + 1;
			
			$params = array(
				"participant[name]" => $team_navn,
				"participant[seed]" => $seedpos
			);
			
			$c->createParticipant($ct->id, $params);
			
			$leader = mysql_result(mysql_query("SELECT leader_id FROM teams WHERE id=$team_id"), 0);
			send_message($leader, "Dit hold er nu fyldt og tilføjet til brackets.", -1);
		}
		//////////////////////////
	}
}

?>