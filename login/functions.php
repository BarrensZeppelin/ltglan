<?php
	if(!$allowIncludes) {
		echo "Includes not allowed.";
		exit;
	}
	
	
	// Funktion til at logge ind i systemet
	function login() {
		
		$billetnr = mysql_real_escape_string($_POST["billetnr"]);
		$pass = md5($_POST["pass"]);
		
		$rows = mysql_num_rows(mysql_query("
			SELECT * FROM guests WHERE billetnr='" . $billetnr . "' AND pass_hashed='" . $pass . "'
		"));
		
		if($rows!=1) {
			return false;
		}
		
		$row = mysql_fetch_array(mysql_query("SELECT * FROM guests WHERE billetnr='" . $billetnr . "' AND pass_hashed='" . $pass . "'"));
		
		$_SESSION["billetnr"] = $billetnr;
		$_SESSION["pass"]  = $pass;
		
		if(mysql_num_rows(mysql_query("SELECT * FROM admins WHERE guest_id=". $row['id'])) >= 1) $_SESSION["admin"] = true;
		else $_SESSION["admin"] = false;
		
		header("Location: ./"); //Redirect til forsiden (index.php)
		return true;
	}
	
	
	// Funktion til at sikre sig, at brugeren der er logget ind, rent faktisk eksisterer i systemet
	function verify_login($checkMySQL=true) { //Hvis variablen ikke er sat, vil den automatisk tjekke om brugeren findes i databasen - ellers vil den kun tjekke om sessionen findes.
		if(isset($_SESSION["billetnr"]) && isset($_SESSION["pass"])) {
			if(!$checkMySQL) {
				return true;
			} else {
				$billetnr = $_SESSION["billetnr"];
				$pass = $_SESSION["pass"];
				
				$rows = mysql_num_rows(mysql_query("
					SELECT * FROM guests
					WHERE billetnr='" . $billetnr . "' AND pass_hashed='" . $pass . "'
				"));
				
				if($rows==1) {
					$row = mysql_fetch_array(mysql_query("SELECT * FROM guests WHERE billetnr='" . $billetnr . "' AND pass_hashed='" . $pass . "'"));
					
					if(mysql_num_rows(mysql_query("SELECT * FROM admins WHERE guest_id=". $row['id'])) >= 1) $_SESSION["admin"] = true;
					else $_SESSION["admin"] = false;
					
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}
	
	
	function get_klasse_array() {
		return array("1. a", "1. b", "1. c", "1. d", "1. e", "1. f", "2. a", "2. b", "2. c", "2. d", "2. e", "2. f", "3. a", "3. b", "3. c", "3. d", "3. e", "3. f", "Anden");
	}
	
	
	function browser_is_valid() {
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/MSIE/i',$u_agent) || preg_match('/Trident/', $u_agent) || preg_match('/Opera/', $u_agent)) {
			return true;
		} else return false;;
	}
	
	
	
	
?>