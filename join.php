<?php
	$allowIncludes = true;
	require "login/includes.php";

	if(!verify_login()) {header("Location: ./");}

	if($_POST["jsenabled"]!="true") {header("refresh: 2; ./"); die("JavaScript er disabled, ingen adgang.");}

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
	
	$query = mysql_query("SELECT * FROM tournaments WHERE id=$id");
	
	if(mysql_num_rows($query) != 1) {
		header("refresh: 2; ./");
		die("Denne turnering findes ikke.");
	}
	
	$turnering = mysql_fetch_array($query);
	
	if($turnering['active'] != 1) {
		header("refresh: 2; ./");
		die("Denne turnering er ikke aktiv.");
	}
	
	
	//Se om spilleren allerede deltager i denne turnering
	$query = mysql_query("SELECT * FROM deltagere WHERE guest_id=".$userid." AND tournament_id=".$id);
	
	
	if(mysql_num_rows($query)>=1) {
		header("refresh: 2; ./");
		die("Du deltager allerede i denne turnering.");
	} 
	
	
	
	
	
	
	
	
	
	
	$turnering_navn = $turnering["navn"];
	$max_spillere = $turnering["max_spillere"];
	$rules = $turnering['rules'];
	
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
		
		/*$target_path="";
		if(isset($_FILES["fileupload"]) && $_FILES["fileupload"]["size"]!=0) {
			if($_FILES["fileupload"]["size"]<=5000000) {
				$target_path = "./imgs/avatars/";
				
				$target_path = $target_path . "avatar-".$id."-".$max_id.".png";
				move_uploaded_file($_FILES['fileupload']['tmp_name'], $target_path);
			} else {
				header("refresh: 2; ./");
				die("Jeres avatar er for stor (maks. 5mb).");
			}
		}*/
		
		///// IMG KODE FRA AUTCHAN
		
		
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
			echo "Du har ikke valgt et billede.<br/>";
		
			$extension = null;
			$target_path = null;
			$size = null;
		}
		
		
		
		///////////////////////////
		
		
		
		//Holdet er oprettet
		mysql_query("INSERT INTO teams (navn, leader_id, teamstatus, avatarpath, tournament_id, bord)
					 VALUES ('" . $holdnavn . "', '" . $userid . "', '" . $status . "', '" . $target_path . "', ". $id . ", ". $bordnr .") ") or die(mysql_error());
		
		$team_id = mysql_result(mysql_query("SELECT id FROM teams WHERE leader_id=$userid AND tournament_id=$id"), 0) or die(mysql_error());
		
		// Læg billedet op
		if($extension != null) {
			imagepng($image, $target_path . "avatar-$team_id.png");
			echo "<img src='". $target_path . "avatar-$team_id.png' /><br/>";
			
			mysql_query("UPDATE teams SET avatarpath='". $target_path . "avatar-$team_id.png' WHERE id=$team_id");
		}
		
		
		//Opret lederen som deltager
		mysql_query("INSERT INTO deltagere (guest_id, tournament_id, team_id, pos)
					VALUES (". $userid .", ". $id .", ". $team_id .", 0)");
		
		for($i=2; $i<=$spillerantal; $i++) {
			if(!isset($_POST["navn" . $i])) continue;
			
			$modtager = $_POST["navn" . $i];
			$modtager = mysql_real_escape_string($modtager);
			
			if($modtager == $userid) continue;
			
			//Lav en ny invitation
			$hash = md5(time() . rand());
			mysql_query("INSERT INTO invites
						 VALUES('" .  $hash . "', '" . $id . "', '" . $team_id . "')");
						 
			//send en besked til hver spiller som er inviteret.
			send_message($modtager, "Du er blevet inviteret til at spille for holdet $holdnavn i ". $turnering_navn ."-turneringen. Hvis du ønsker at acceptere, så klik <a href=\'accept_invite.php?hash=" . $hash . "\'>her</a>");			
		}
		
		echo mysql_error();
		
		header("refresh: 3; ./");
		die("Dit hold er blevet oprettet og der er blevet sendt invites ud til dem du har inviteret. Du vil automatisk blive redirected.");
	}
?>
		