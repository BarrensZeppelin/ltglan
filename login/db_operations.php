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
	
		mysql_query("DELETE FROM deltagere WHERE id=". $deltager['id']);
		mysql_query("UPDATE teams SET teamstatus='Pending' WHERE id=". $team['id']) or die(mysql_error());
		mysql_query("DELETE FROM beskeder WHERE indhold LIKE '%" . $team["navn"] . "%" . $turnering_navn . "%' AND modtager_id=". $deltager['guest_id']) or die(mysql_error());
		
		$pos = $deltager['pos'];
		
		if($pos != (mysql_result(mysql_query("SELECT MAX(pos) FROM deltagere WHERE team_id=". $team['id']), 0)+1)) {
			$deltager2 = mysql_fetch_array(mysql_query("SELECT * FROM deltagere WHERE team_id=". $team['id'] ." ORDER BY pos DESC LIMIT 1"));
			mysql_query("UPDATE deltagere SET pos=$pos WHERE id=". $deltager2['id']);
		}
	}
}

?>