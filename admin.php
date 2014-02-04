

<?php

	require 'login/includes.php';
	
	if(!verify_login()) {header("Refresh: 2; ./"); die("Log ind først. :D");}
	
	if(!isset($_SESSION['admin']) || $_SESSION["admin"] == false) {
		header("Refresh: 2; ./");
		die("Du er ikke admin. o:");
	}
	?> <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> <?php
	
	if(!isset($_GET["page"])) {
		?>
		
		<a href="admin.php?page=guests">Guests</a> <br/>
		<a href="admin.php?page=brackets">Brackets & Tournaments</a>
		
		<?php
	} else if($_GET["page"] == "guests") {
		if(isset($_GET["del"])) {
			$id = $_GET["del"];
			$guest = mysql_fetch_array(mysql_query("SELECT * FROM guests WHERE id=$id"));
			
			mysql_query("DELETE FROM beskeder WHERE modtager_id=$id");
			mysql_query("DELETE FROM guests WHERE id=$id");
			
			$deltager_query = mysql_query("SELECT * FROM deltagere WHERE guest_id=$id");
			while($deltager = mysql_fetch_array($deltager_query)) {
				slet_deltager($deltager['id']);
			}
			
			header("Location: ./admin.php?page=guests");
			die($guest['navn'] ." blev slettet.");
			
			
			
		} else if(isset($_GET['admin'])) {
			
			if(mysql_num_rows(mysql_query("SELECT * FROM admins WHERE guest_id=". $_GET['admin'])) == 0) {
				mysql_query("INSERT INTO admins (guest_id) VALUES (". $_GET['admin'] .")");
			} else {
				mysql_query("DELETE FROM admins WHERE guest_id=". $_GET['admin']);
			}
			
			header("Location: ./admin.php?page=guests");
			die();
			
		} else if(isset($_POST['billetnr'])) {
			mysql_query("INSERT INTO guests (pass_hashed, billetnr, navn, klasse)
						VALUES('". md5($_POST["password"]) ."', ". $_POST["billetnr"] .", '". $_POST["navn"] ."', '". $_POST["klasse"] ."')");
						
			header("Location: ./admin.php?page=guests");
			die();
		} else {
			$tcontent = "<tr><th>id</th><th>billetnr</th><th>navn</th><th>klasse</th><th>slet</th><th>admin</th></tr>";
			
			$query = mysql_query("SELECT * FROM guests ORDER BY id ASC");
			while( $guest = mysql_fetch_array($query) ) {
				$tcontent .= "<tr>
					<td>". $guest["id"] ."</td>
					<td>". $guest["billetnr"] ."</td>
					<td><span style='". (mysql_num_rows(mysql_query("SELECT * FROM admins WHERE guest_id=". $guest['id'])) == 1 ? "font-weight:bold;" : "") ."'>". $guest["navn"] ."</span></td>
					<td>". $guest["klasse"] ."</td>
					<td><a href='admin.php?page=guests&del=". $guest['id'] ."'>X</a></td>
					<td style='text-align:center;'><a href='admin.php?page=guests&admin=". $guest['id'] ."'>X</a></td>
				</tr>";
			}
			
			echo "<a href='./admin.php'>Back</a><br/><br/>";
			echo "<form action='./admin.php?page=guests' method='post'>
					Billetnr: <input type='text' name='billetnr' />  Password: <input type='text' name='password' />  Navn: <input type='text' name='navn' />  Klasse: <input type='text' name='klasse' /> <input type='submit' value='Ny' />
				</form><br/>";
			echo "Admins er i bold<br/>";
			echo "<table><tbody>". $tcontent ."</tbody></table>";
		}
	} else if($_GET["page"] == "brackets") {
		
		if(isset($_GET['tid'])) {
			
			if(isset($_POST['link'])) {
				mysql_query("UPDATE tournaments SET bracketlink='". $_POST['link'] ."' WHERE id=". $_GET['tid']) or die(mysql_error());
			} else if(isset($_GET['rm'])) { // Remove link
				mysql_query("UPDATE tournaments SET bracketlink='' WHERE id=". $_GET['tid']) or die(mysql_error());
			} else if(isset($_GET['activate'])) {
				$current = mysql_result(mysql_query("SELECT active FROM tournaments WHERE id=". $_GET['tid']), 0);
				mysql_query("UPDATE tournaments SET active=". ($current == 0 ? 1 : 0) ." WHERE id=". $_GET['tid']) or die(mysql_error());
			}
			
			header("Location: ./admin.php?page=brackets");
			
		} else {

			$tcontent = "<tr><th>Navn</th><th>bracket link</th><th>Activate/Deactivate</th></tr>";
			
			$t_query = mysql_query("SELECT * FROM tournaments ORDER BY id ASC");
			while($t = mysql_fetch_array($t_query)) {
				$tcontent .= "<tr>
								<td><span style='". ($t['active'] == 0 ? "font-style: italic;" : "font-weight: bold;") ."'> ". $t['navn'] ."</span></td>";
				
				if($t['bracketlink'] == "") {
					$tcontent .= "<td><form style='margin:0' action='./admin.php?page=brackets&tid=". $t['id'] ."' method='post'>Link: <input type='text' name='link' /> <input type='submit' value='Submit' /></form></td>";
				} else {
					$tcontent .= "<td><a href='http://challonge.com/". $t['bracketlink'] ."' target='_blank'>". $t['bracketlink'] ."</a> <a href='./admin.php?page=brackets&tid=". $t['id'] ."&rm'>X</a></td>";
				}
				
				$tcontent .= "<td style='text-align: center;'><a href='./admin.php?page=brackets&tid=". $t['id'] ."&activate'>X</a></td>";
				
				$tcontent .= "</tr>";
			}
			
			echo "<a href='./admin.php'>Back</a><br/><br/>";
			echo "<table><tbody>". $tcontent ."</tbody></table>";
		}
	
	} else {
		header("Location: ./admin.php");
	}
	
?>
