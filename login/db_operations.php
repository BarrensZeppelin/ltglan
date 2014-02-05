
<?php 

function send_message($modtager, $indhold) {
	mysql_query("INSERT INTO beskeder (modtager_id, indhold)
				 VALUES ($modtager, '$indhold')") or die(mysql_error());
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

	$turnering_navn = mysql_result(mysql_query("SELECT navn FROM tournaments WHERE id=". $team['tournament_id']), 0);
		
	if($team['avatarpath'] != null && $team['avatarpath'] != "") unlink($team['avatarpath']); // Slet avatar

	mysql_query("DELETE FROM teams WHERE id=" . $team['id']) or die(mysql_error());
	mysql_query("DELETE FROM deltagere WHERE team_id=". $team['id']) or die(mysql_error());
	mysql_query("DELETE FROM invites WHERE team_id=" . $team['id']) or die(mysql_error());
	mysql_query("DELETE FROM beskeder WHERE indhold LIKE '%" . $team["navn"] . "%" . $turnering_navn . "%'") or die(mysql_error());
}


function slet_deltager($deltagerid) {
	$deltager = get_deltager($deltagerid);
	
	$team = get_team($deltager['team_id']);
	if($deltager['pos'] == 0) {	
		
		slet_hold($team['id']);
		
	} else {
		$turnering_navn = mysql_result(mysql_query("SELECT navn FROM tournaments WHERE id=". $team['tournament_id']), 0);
	
		mysql_query("DELETE FROM deltagere WHERE id=". $deltager['id']);
		mysql_query("UPDATE teams SET teamstatus='Pending' WHERE id=". $team['id']) or die(mysql_error());
		mysql_query("DELETE FROM beskeder WHERE indhold LIKE '%" . $team["navn"] . "%" . $turnering_navn . "%' AND modtager_id=". $deltager['guest_id']) or die(mysql_error());
		
		$max_spillere = mysql_result(mysql_query("SELECT max_spillere FROM tournaments WHERE id=". $team['tournament_id']), 0);
		$pos = $deltager['pos'];
		
		if($pos != (mysql_result(mysql_query("SELECT MAX(pos) FROM deltagere WHERE team_id=". $team['id']), 0)+1)) {
			$deltager2 = mysql_fetch_array(mysql_query("SELECT * FROM deltagere WHERE team_id=". $team['id'] ." ORDER BY pos DESC LIMIT 1"));
			mysql_query("UPDATE deltagere SET pos=$pos WHERE id=". $deltager2['id']);
		}
	}
}

?>