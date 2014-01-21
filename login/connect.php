<?php
	if(!$allowIncludes) {
		echo "Includes not allowed.";
		exit;
	}

	

	/*$mconnect = mysql_connect("localhost", "autistklassen_d", "QVL5DtF5"); //
	if(!$mconnect) {
		die("ERROR! Unable to connect to MySQL.");
	}
	mysql_select_db("autistklassen_d") or die("ERROR! Unable to select database!");
	*/
	
	$mconnect = mysql_connect("localhost", "root", ""); //
	if(!$mconnect) {
		die("ERROR! Unable to connect to MySQL.");
	}
	mysql_select_db("ltglan") or die("ERROR! Unable to select database!");
	
	mysql_set_charset("utf8");
	
	?>