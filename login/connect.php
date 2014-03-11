<?php
	if(!$allowIncludes) {
		echo "Includes not allowed.";
		exit;
	}


	if(!mysql_connect("localhost", "root", "")) {
		die("ERROR! Unable to connect to MySQL.");
	}
	mysql_select_db("ltglan") or die("ERROR! Unable to select database!");

	mysql_set_charset("utf8");

?>