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
		post_to("./?p=tournaments", array("alert" => "Invitationen kunne ikke findes i databasen."));
	}
	
	$billetnr = intval($_SESSION['billetnr']);
	$userid = mysql_result(mysql_query("SELECT id FROM guests WHERE billetnr=$billetnr"), 0);
	
	
	$team_id = mysql_result(mysql_query("SELECT team_id FROM invites WHERE hash='$hash'"),0);
	
	// Opret deltageren
	ny_deltager($userid, $team_id, $hash);
	
	header("Location: ./?p=tournaments");
	die("Du er nu medlem af holdet $team_navn");
?>