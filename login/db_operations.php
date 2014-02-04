
<?php 

function send_message($modtager, $indhold) {
	mysql_query("INSERT INTO beskeder (modtager_id, indhold)
				 VALUES ($modtager, '$indhold')") or die(mysql_error());
}


// Simple ID Fetches

function get_deltager($id) {
	$deltagerquery = mysql_query("SELECT FROM deltagere WHERE id=$id");
	
	return mysql_fetch_array($deltagerquery) or die(mysql_error());
}

function get_guest($id) {
	$guestquery = mysql_query("SELECT FROM guests WHERE id=$id");
	
	return mysql_fetch_array($guestquery) or die(mysql_error());
}

function get_team($id) {
	$teamquery = mysql_query("SELECT FROM teams WHERE id=$id");
	
	return mysql_fetch_array($teamquery) or die(mysql_error());
}

////////////////////////////////





function slet_deltager($deltagerid) {
	$deltager = mysql_fetch_array(mysql_query("SELECT * FROM deltagere WHERE id=$deltagerid"));

	$team = mysql_fetch_array(mysql_query("SELECT * FROM teams WHERE id=". $deltager['team_id']));
	if($deltager['pos'] == 0) {	
		$turnering_navn = mysql_result(mysql_query("SELECT navn FROM tournaments WHERE id=". $team['tournament_id']), 0);
		
		if($team['avatarpath'] != null && $team['avatarpath'] != "") unlink($team['avatarpath']); // Slet avatar

		mysql_query("DELETE FROM teams WHERE id=" . $team['id']) or die(mysql_error());
		mysql_query("DELETE FROM deltagere WHERE team_id=". $team['id']) or die(mysql_error());
		mysql_query("DELETE FROM invites WHERE team_id=" . $team['id']) or die(mysql_error());
		mysql_query("DELETE FROM beskeder WHERE indhold LIKE '%" . $team["navn"] . "%" . $turnering_navn . "%'") or die(mysql_error());
		
	} else {
		mysql_query("DELETE FROM deltagere WHERE id=". $deltager['id']);
		mysql_query("UPDATE teams SET status='Pending' WHERE id=". $team['id']);
		
		$max_spillere = mysql_result(mysql_query("SELECT max_spillere FROM tournaments WHERE id=". $team['tournament_id']), 0);
		$pos = $deltager['pos'];
		
		if($pos != (mysql_result(mysql_query("SELECT MAX(pos) FROM deltagere WHERE team_id=". $team['id']), 0)+1)) {
			$deltager2 = mysql_fetch_array(mysql_query("SELECT * FROM deltagere WHERE team_id=". $team['id'] ." ORDER BY pos DESC LIMIT 1"));
			mysql_query("UPDATE deltagere SET pos=$pos WHERE id=". $deltager2['id']);
		}
	}
}

?>