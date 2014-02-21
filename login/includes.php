<?php
	//Her kalder vi alle funktionerne, og sætter globale variabler
	if(session_id() == '') {
		session_start();
	}
	
	$allowIncludes = true; //Alle filer (undtagen index.php og denne fil) tjekker om $allowIncludes er sat - hvis den ikke er, så loader den ikke siden.
	
	include "scripts/challonge.class.php";
	include "login/connect.php";
	include "login/functions.php";
	include "login/db_operations.php";
?>