<?php
	$allowIncludes = true;
	require "login/includes.php";

	if(!verify_login()) {header("Location: ./");}

	// Vi tjekker om javascript var enabled da formen fra join-popup-page.php blev submittet
	// Var den ikke det, bliver dataene ikke tjekket, og der kan opstå problemer
	if($_POST["jsenabled"]!="true") {header("refresh: 2; ./"); die("JavaScript er disabled, ingen adgang.");}

	$klassearray = get_klasse_array();
	$billetnr = intval($_SESSION['billetnr']);
	$guest = get_guest_wbilletnr($billetnr);
	
	
	$user_klasse = $guest["klasse"];
	$navn = $guest["navn"];
	$userid = $guest["id"];
	
	
	//tournament id
	$id = intval($_GET['id']);
	
	$query = mysql_query("SELECT * FROM tournaments WHERE id=$id");
	
	if(mysql_num_rows($query) != 1) {
		header("refresh: 2; ./");
		die("Denne turnering findes ikke.");
	}
	
	$turnering = get_tournament($id);
	
	if($turnering['active'] != 1) {
		header("refresh: 2; ./");
		die("Denne turnering er ikke aktiv.");
	}
	
	
	//Se om spilleren allerede deltager i denne turnering
	$query = mysql_query("SELECT * FROM deltagere WHERE guest_id=$userid");
	while( $deltager = mysql_fetch_array($query) ) {
		$team = get_team($deltager['team_id']);
		if($team['tournament_id'] == $id) {
			header("Refresh: 2; ./");
			die("Du deltager allerede i denne turnering.");
		} 
	}
	
	
	
	
	
	
	$extramessage = "";
	
	$turnering_navn = $turnering["navn"];
	$max_spillere = $turnering["max_spillere"];
	$rules = $turnering['rules'];
	
	if(isset($_POST["playercounter"])) {
		$spillerantal = intval($_POST["playercounter"]);

		$holdnavn = mysql_real_escape_string(htmlspecialchars($_POST["holdnavn"]));
		
		$bordnr = intval($_POST["bordnr"]);
		
		if($max_spillere>1) {
			$status = "Pending";
		} else if($max_spillere==1) {
			$status = "Accepted";
		}
		
		
		
		// Behandl billedet så det bliver resizet korrekt //
		$extension = null;
		$max_file_size = 5000000; // 5MB
		$target_path = "./imgs/avatars/";
		
		if(!empty($_FILES["fileupload"]["type"])) {
			$imgname = $_FILES["fileupload"]["name"];
			$extpos    = strrpos($imgname, "."); //Giver punktet hvor det sidste punktum i filnavnet er.
			$extension = substr($imgname, $extpos);
			$extension = strtolower(mysql_real_escape_string($extension));
			
			if ((($extension !== ".gif")
			  && ($extension !== ".png")
			  && ($extension !== ".jpg")
			  && ($extension !== ".jpeg"))) {
				header("refresh: 2; ./");
				die("Image filetype is not valid");
			} else if($_FILES["fileupload"]["size"] > $max_file_size) {
				header("refresh: 2; ./");
				die("Filesize is too large (Max: $max_file_size bytes.)"); //Filen er for stor
			}
			$size = $_FILES["fileupload"]["size"];
			
			$src = $_FILES['fileupload']['tmp_name'];

			
			list($width, $height) = getimagesize($src);

			if(($width > 125 || $height > 125) || ($width != 125 && $height != 125)) {
				if($width>=$height) {
					$newwidth = 125;
					$newheight = floor($height * 125/$width);
				} else {
					$newwidth = floor($width * 125/$height);
					$newheight = 125;
				}
				
			}
			
			//Kilde filen defineres...
			if($extension==".gif") $source = imagecreatefromgif($src);
			else if($extension==".png") $source = imagecreatefrompng($src);
			else if($extension==".jpg" || $extension==".jpeg") $source = imagecreatefromjpeg($src);
			
			//Det nye billed defineres med den nye længde & bredde.
			$image = imagecreatetruecolor($newwidth, $newheight);
			
			//Sæt $image til at være lig $source - bare resized!
			imagecopyresampled($image, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
			
		} else {
			$extramessage .= "Du har ikke valgt et billede.<br/>";
		
			$extension = null;
			$target_path = null;
			$size = null;
		}
		///////////////////////////////////////
		
		
		
		//Holdet er oprettet
		mysql_query("INSERT INTO teams (navn, leader_id, teamstatus, avatarpath, tournament_id, bord)
					 VALUES ('" . $holdnavn . "', '" . $userid . "', '" . $status . "', '" . $target_path . "', ". $id . ", ". $bordnr .") ") or die(mysql_error());
		
		$team_id = mysql_result(mysql_query("SELECT id FROM teams WHERE leader_id=$userid AND tournament_id=$id"), 0) or die(mysql_error());
		
		// Læg billedet op
		if($extension != null) {
			imagepng($image, $target_path . "avatar-$team_id.png");
			$extramessage .= "<img src='". $target_path . "avatar-$team_id.png' /><br/>";
			
			mysql_query("UPDATE teams SET avatarpath='". $target_path . "avatar-$team_id.png' WHERE id=$team_id");
		}
		
		
		//Opret lederen som deltager
		mysql_query("INSERT INTO deltagere (guest_id, team_id, pos)
					VALUES (". $userid .", ". $team_id .", 0)");
		
		
		// Inviter de valgte brugere
		for($i=2; $i<=$spillerantal; $i++) {
			if(!isset($_POST["navn" . $i])) continue; // Der er ikke valgt nogen bruger til denne position
			
			$modtager = intval($_POST["navn" . $i]);
			
			if($modtager == $userid) continue; // Burde ikke kunne ske, men for sikkerheds skyld skal man ikke kunne invitere sig selv
			
			//Lav en ny invitation
			$hash = md5(time() . rand());
			mysql_query("INSERT INTO invites
						 VALUES('" .  $hash . "', '" . $team_id . "')");
						 
						 
			$guest = get_guest($modtager);
			
			//send en besked til hver spiller som er inviteret.
			send_message($modtager, "Du er blevet inviteret til at spille for holdet $holdnavn i ". $turnering_navn ."-turneringen. Hvis du ønsker at acceptere, så klik <a href=\'accept_invite.php?hash=" . $hash . "\'>her</a>");			
			send_message($userid, "Du inviterede ". $guest['navn'] ." til $holdnavn i ". $turnering_navn ."-turneringen.", -1);
		}
		
		header("Location: ./");
		die($extramessage . "Dit hold er blevet oprettet og der er blevet sendt invites ud til dem du har inviteret. Du vil automatisk blive redirected.");
	}
?>