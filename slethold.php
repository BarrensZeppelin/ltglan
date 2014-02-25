<?php
	$allowIncludes = true;
	require "login/includes.php";

	if(!verify_login()) {post_to("./?p=front", array("alert" => "Du er ikke logget ind."));}
	
	
	if(!isset($_GET["tid"], $_GET["id"])) {
		header("Location: ./");
		exit;
	}
	
	$tid = intval($_GET["tid"]);
	$id = intval($_GET["id"]);
	
	$turnering_navn = mysql_result(mysql_query("SELECT navn FROM tournaments WHERE id=$tid"), 0);
	
	$team = get_team(id);
	
	
	$bruger = get_guest_wbilletnr(intval($_SESSION['billetnr']));
	if($team['leader_id'] == $bruger['id']) {
		slet_hold($id);
		
		header("Location: ./");
		die("Dit hold er blevet slettet. Du omdirigeres til hovedsiden om 2 sekunder.");
	}
	
?>