
<?php 

function send_message($modtager, $indhold) {
	mysql_query("INSERT INTO beskeder (modtager_id, indhold)
				 VALUES ($modtager, '$indhold')") or die(mysql_error());
}

function get_deltager($id) {
	$deltagerquery = mysql_query("SELECT FROM deltagere WHERE id=$id");
	
	return mysql_fetch_array($deltagerquery) or die(mysql_error());
}


?>