<?php
	$allowIncludes = true;
	require "login/includes.php";

	if(!verify_login()) {header("Location: ./");}

	if($_POST["jsenabled"]!="true") {die("JavaScript er disabled, ingen adgang.");}

	$klassearray = get_klasse_array();
	$billetnr = $_SESSION['billetnr'];
	$query = mysql_query("SELECT * FROM guests WHERE billetnr='$billetnr'");
	$row = mysql_fetch_array($query);
	
	
	$user_klasse = $row["klasse"];
	$navn = $row["navn"];
	$userid = $row["id"];
	
	
	//tournament id
	$id = $_GET["id"];
	$id = mysql_real_escape_string($id);
	
	
	$max_turneringer = mysql_query("SELECT MAX(id) FROM tournaments");
	$max_turneringer = mysql_result($max_turneringer,0);
	
	//Se om spilleren allerede deltager i denne turnering
	$query = mysql_query("SELECT * FROM deltagere WHERE guest_id=".$userid." AND tournament_id=".$id);
	
	
	if($id>$max_turneringer or mysql_num_rows($query)>=1) {
		header("Location: ./");
		exit;
	} 
	
	
	
	
	
	
	
	
	$arr = mysql_fetch_array(mysql_query("SELECT * FROM tournaments WHERE id='" . $id . "'"));
	
	$turnering_navn = $arr["navn"];
	$max_spillere = $arr["max_spillere"];
	$rules = $arr['rules'];
	
	if(isset($_POST["playercounter"])) {
		$spillerantal = $_POST["playercounter"];
		
		$holdnavn = $_POST["holdnavn"];
		$holdnavn = mysql_real_escape_string($holdnavn);
		
		$bordnr = $_POST["bordnr"];
		$bordnr = mysql_real_escape_string($bordnr);
		$bordnr = intval($bordnr);
		
		if($max_spillere>1) {
			$status = "Pending";
		} else if($max_spillere==1) {
			$status = "Accepted";
		}
		
		$target_path="";
		if(isset($_FILES["fileupload"]) && $_FILES["fileupload"]["size"]!=0 && $_FILES["fileupload"]["size"]<=50000000) {
			$target_path = "./imgs/avatars/";
			
			$target_path = $target_path . "avatar-".$id."-".$max_id.".png";
			move_uploaded_file($_FILES['fileupload']['tmp_name'], $target_path);
		}
		
		
		//Holdet er oprettet
		mysql_query("INSERT INTO teams (navn, leader_id, teamstatus, avatarpath, tournament_id, bord)
					 VALUES ('" . $holdnavn . "', '" . $userid . "', '" . $status . "', '" . $target_path . "', ". $id . ", ". $bordnr .") ") or die(mysql_error());
		
		$team_id = mysql_result(mysql_query("SELECT id FROM teams WHERE leader_id=$userid AND tournament_id=$id"), 0) or die(mysql_error());
		
		//Opret lederen som deltager
		mysql_query("INSERT INTO deltagere (guest_id, tournament_id, team_id, pos)
					VALUES (". $userid .", ". $id .", ". $team_id .", 0)");
		
		for($i=2; $i<=$spillerantal; $i++) {
		
			$modtager = $_POST["navn" . $i];
			$modtager = mysql_real_escape_string($modtager);
			
			if($modtager == $userid) continue;
			
			//Lav en ny invitation
			$hash = md5(time() . rand());
			mysql_query("INSERT INTO invites
						 VALUES('" .  $hash . "', '" . $id . "', '" . $team_id . "')");
						 
			//send en besked til hver spiller som er inviteret.				
			mysql_query("INSERT INTO beskeder (modtager_id, indhold, laest)
						 VALUES ('" . $modtager . "', 'Du er blevet inviteret til at spille for holdet " . $holdnavn . " i ". $turnering_navn ."-turneringen. Hvis du ønsker at acceptere, så klik <a href=\'accept_invite.php?hash=" . $hash . "\'>her</a>', '0')");
		}
		
		echo mysql_error();
		
		header("refresh: 3; ./");
		die("Dit hold er blevet oprettet og der er blevet sendt invites ud til dem du har inviteret. Du vil automatisk blive redirected.");
	}
?>
		