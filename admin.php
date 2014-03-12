<?php

	require 'login/includes.php';
	
	if(!verify_login()) {post_to("./?p=front", array("alert" >= "Log ind først. :D"));}
	
	if(!isset($_SESSION['admin']) || $_SESSION["admin"] == false) {
		post_to("./?p=tournaments", array("alert" => "Du er ikke admin. o:"));
	}
	
	if(!isset($_GET["page"])) {
		?>
		
		<a href="admin.php?page=guests">Guests</a> <br/>
		<a href="admin.php?page=deltagere">Deltagere</a> <br/>
		<a href="admin.php?page=teams">Teams</a> <br/>
		<a href="admin.php?page=brackets">Brackets & Tournaments</a>
		
		<?php
	} else {

	if($_GET["page"] == "guests") {
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
		
			$tcontent = "<tr><th><a onclick='document.forms[\"qform\"].elements[\"query\"].value=\"ORDER BY id ASC\";document.forms[\"qform\"].submit();'>id</a></th><th><a onclick='document.forms[\"qform\"].elements[\"query\"].value=\"ORDER BY billetnr ASC\";document.forms[\"qform\"].submit();'>billetnr</a></th><th>navn</th><th>klasse</th><th>slet</th><th>admin</th></tr>";
			
			$query = mysql_query("SELECT * FROM guests ". (isset($_POST['query']) ? $_POST['query'] : "ORDER BY id ASC")) or die(mysql_error());
			if(isset($_GET['id'])) $query = mysql_query("SELECT * FROM guests WHERE id=". $_GET['id']);
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
			echo "Custom query: <form name='qform' action='./admin.php?page=guests' method='post'><span style='background-color:grey'>SELECT * FROM guests </span><input type='text' name='query' value='". (isset($_GET['id']) ? "WHERE id=". $_GET['id'] : (isset($_POST['query']) ? $_POST['query'] : "")) ."' /><input type='submit' value='Submit' /></form>"; 
			echo "Admins er i bold<br/>";
			echo "<table><tbody>". $tcontent ."</tbody></table>";
		}
	} else if($_GET["page"] == "brackets") {
		
		if(isset($_POST['name'])) {
			$id = mysql_result(mysql_query("SELECT MAX(id) FROM tournaments"), 0) + 1;
		
			mysql_query("INSERT INTO tournaments (id, navn, short, max_spillere)
						VALUES($id,'". $_POST['name'] ."','". $_POST['short'] ."',". $_POST['max_spillere'] .")") or die(mysql_error());
			
			header("Location: ./admin.php?page=brackets");
			exit;
		}
		
		if(isset($_GET['tid'])) {
			if(isset($_POST['tstyle'])) {
				if($_POST['tstyle'] != "") {
					mysql_query("UPDATE tournaments SET tournament_style='". $_POST['tstyle'] ."' WHERE id=". $_GET['tid']);
				}
			} else if(isset($_GET['rmtstyle'])) {
				mysql_query("UPDATE tournaments SET tournament_style='' WHERE id=". $_GET['tid']);
			} else if(isset($_GET['activate'])) {
				$current = mysql_result(mysql_query("SELECT active FROM tournaments WHERE id=". $_GET['tid']), 0);
				mysql_query("UPDATE tournaments SET active=". ($current == 0 ? 1 : 0) ." WHERE id=". $_GET['tid']) or die(mysql_error());
			} else if(isset($_GET['del_bracket'])) {
			
				$tid = intval($_GET['tid']);
				
				$mt = get_tournament($tid);
				if(isset($mt['bracketlink']) && $mt['bracketlink'] != "") {
				
					$c = connect_challonge();
				
					// Delete tourny
					$c->makeCall("tournaments/". $mt['bracketlink'], array(), "delete");
					
					mysql_query("UPDATE tournaments SET bracketlink='' WHERE id=". $mt['id']) or die(mysql_error());
					
					$query = mysql_query("SELECT * FROM teams WHERE tournament_id=$tid") or die(mysql_error());
					while( $t = mysql_fetch_array($query) ) {
						mysql_query("DELETE FROM beskeder WHERE modtager_id=". $t['leader_id'] ." AND indhold LIKE '%midlertidig bracket%". $mt['navn'] ."%'") or die(mysql_error());
					}
				}
			
			} else if(isset($_GET['create_bracket'])) {
				$tid = intval($_GET['tid']);
				
				$mt = get_tournament($tid);
				if(!isset($mt['bracketlink']) || $mt['bracketlink'] == "") {
					$c = connect_challonge();
					
					$mons = array(1 => "January", 2 => "February", 3 => "March", 4 => "April", 5 => "May", 6 => "June", 7 => "July", 8 => "August", 9 => "September", 10 => "October", 11 => "November", 12 => "December");

					$date = getdate();
					$month = $date['mon'];

					$month_name = $mons[$month];

				
					$params = array(
					  "tournament[name]" => "LTGLAN " . strtoupper($mt['short']) . " $month_name " . date("Y"),
					  "tournament[tournament_type]" => $mt['tournament_style'],
					  "tournament[url]" => "ltglan_" . $mt['short'] . "_" . strtolower($month_name) . "_" . date('y'),
					  "tournament[description]" => "LTGLANs " . $mt['navn'] . "-turnering $month_name ". date('Y'),
					  "tournament[open_signup]" => "false"
					);
					
					if($mt['tournament_style'] == "single elimination") $params['tournament[hold_third_place_match]'] = "true";
					
					$ct = $c->createTournament($params);
					
					// Add Teams
					$i = 1;
					$query = mysql_query("SELECT * FROM teams WHERE tournament_id=". $mt['id'] ." AND teamstatus='Accepted' ORDER BY seed ASC, id ASC");
					while($team = mysql_fetch_array($query)) {
						
						$params = array(
							"participant[name]" => $team['navn'],
							"participant[seed]" => $i
						);
						
						$c->createParticipant($ct->id, $params);
					
						$i++;
					}
					
					
					// Send besked til alle teams i turneringen
					$query = mysql_query("SELECT * FROM teams WHERE tournament_id=". $mt['id']);
					while($team = mysql_fetch_array($query)) {
						send_message($team['leader_id'], "En midlertidig bracket er blevet oprettet for ". $mt['navn'] ."-turneringen. Er dit hold ikke fyldt (Accepteret/Grønt) når turneringen starter, bliver dit hold slettet og holdet deltager ikke i turneringen. 
						På samme måde optræder holdet først i bracket\'en, når det er fyldt helt op.", -1);
					}
					
					
					mysql_query("UPDATE tournaments SET bracketlink='". $ct->url ."' WHERE id=". $mt['id']) or die(mysql_error()); 
					
				}	
			} else if(isset($_GET['start'])) {
				$tid = intval($_GET['tid']);
				$mt = get_tournament($tid);
				if(isset($mt['bracketlink']) && $mt['bracketlink'] != "" && mysql_num_rows(mysql_query("SELECT * FROM teams WHERE tournament_id=". $mt['id'] ." AND teamstatus='Accepted'")) >= 2) {
					// Get and start tournament
					$c = connect_challonge();
					
					$ct = $c->makeCall("tournaments/" . $mt['bracketlink'], array("include_matches" => 0), "get");
					
					$c->startTournament($ct->id, array());
					
					// Send messages
					$query = mysql_query("SELECT * FROM teams WHERE tournament_id=". $mt['id']);
					while($team = mysql_fetch_array($query)) {
						if($team['teamstatus'] == "Pending") {
							slet_hold($team['id']);
							send_message($team['leader_id'], $mt['navn'] ."-turneringen er startet og dit hold var ikke fyldt, det betyder, at I desværre ikke kommer med i turneringen.", -1);
						} else {
							send_message($team['leader_id'], $mt['navn'] ."-turneringen er startet og I kan se de endelige brackets nu. Held og lykke!", -1);
						}
					}
					
					// Update reg_open
					mysql_query("UPDATE tournaments SET reg_open=0 WHERE id=". $mt['id']) or die(mysql_error());
				}
			}
			
			header("Location: ./admin.php?page=brackets");
			exit;
			
		} else {

			$tcontent = "<tr><th>navn</th><th>short</th><th>bracket link</th><th>Activate / <br/>Deactivate</th></tr>";
			
			$t_query = mysql_query("SELECT * FROM tournaments ORDER BY id ASC");
			while($t = mysql_fetch_array($t_query)) {
				$tcontent .= "<tr>
								<td><span style='". ($t['active'] == 0 ? "font-style: italic;" : "font-weight: bold;") ."'> ". $t['navn'] ."</span></td>
								<td>". $t['short'] ."</td>";
				
				if($t['bracketlink'] == "") {
					if($t['tournament_style'] == "") {
						$tcontent .= "<td><form action='./admin.php?page=brackets&tid=". $t['id'] ."' method='post' style='margin:0'>
							<select name='tstyle'>
								<option value=''></option><option value='single elimination'>single elimination</option><option value='double elimination'>double elimination</option>
								<option value='round robin'>round robin</option><option value='swiss'>swiss</option>
							</select>
							<input type='submit' value='X' />
						</form></td>";
					}
					else $tcontent .= "<td><a href='./admin.php?page=brackets&tid=". $t['id'] ."&create_bracket'>create bracket</a> <a href='./admin.php?page=brackets&tid=". $t['id'] ."&rmtstyle'>X</a></td>";
				} else {
					if($t['reg_open'] == 1)
						$tcontent .= "<td><a href='http://challonge.com/". $t['bracketlink'] ."' target='_blank'>". $t['bracketlink'] ."</a>";
					else $tcontent .= "<td><a href='http://challonge.com/". $t['bracketlink'] ."' target='_blank'><b>". $t['bracketlink'] ."</b></a>";
					
					if(mysql_num_rows(mysql_query("SELECT * FROM teams WHERE tournament_id=". $t['id'] ." AND teamstatus='Accepted'")) >= 2 && $t['reg_open'] == 1) $tcontent .= " <a href='./admin.php?page=brackets&tid=". $t['id'] ."&start'><b>START</b></a>";
					if($t['reg_open'] == 1) $tcontent .= " <a href='./admin.php?page=brackets&tid=". $t['id'] ."&del_bracket'><i>DELETE</i></a></td>";
					else $tcontent .= "</td>";
				}
				
				$tcontent .= "<td style='text-align: center;'><a href='./admin.php?page=brackets&tid=". $t['id'] ."&activate'>X</a></td>";
				
				$tcontent .= "</tr>";
			}
			
			echo "<a href='./admin.php'>Back</a><br/><br/>";
			echo "<form name='new_tournament' method='post' action='./admin.php?page=brackets'>
					navn: <input type='text' name='name' /> short: <input type='text' name='short' /> teamsize: <input type='number' name='max_spillere' />
					<select name='tstyle'>
						<option value=''>no brackets</option><option value='single elimination'>single elimination</option>
						<option value='double elimination'>double elimination</option><option value='round robin'>round robin</option><option value='swiss'>swiss</option>
					</select>
					<input type='submit' />
				</form>";
			echo "<b>Artwork:</b><br/>
					<div style='display:inline-block;margin-left:20px;'>
						Tournament banners:   <i>./imgs/tournament-<b>id</b>.png</i>          (540x100)<br/>
						Tournament backdrops: <i>./imgs/tournament-<b>id</b>-backdrop.png</i> (960x610)
					</div><br/><br/>";
			?>
			
			<script type='text/javascript'>
				function showChallonge() {
					document.getElementById("challonge").innerHTML = "<div style='display:inline-block;float:left;'>username: <i>ltglan</i><br/>password: <i>challonge</i></div><div style='display:inline-block;margin-left:20px;margin-top:7px;'><a href='http://challonge.com/'>challonge.com</a> - <a href='#' onclick='hideChallonge()'>hide</a></div>";
				}
				
				function hideChallonge() {
					document.getElementById("challonge").innerHTML = "<a href='#' onclick='showChallonge()'><i>Show LTGLAN Challonge Login</i></a>";
				}
			</script>
			
			<?php
			echo "<b>Report winners and manage brackets further with Challonge:</b><br/>
				<div id='challonge' style='display:inline-block;margin-left:20px;'>
					<a href='#' onclick='showChallonge()'><i>Show LTGLAN Challonge Login</i></a>
				</div><br/>";
			echo "<table><tbody>". $tcontent ."</tbody></table>";
		}
	
	} else if($_GET['page'] == "deltagere") {
		if(isset($_POST['guest_id'])) {
			
			$guest_id = intval($_POST['guest_id']);
			$team_id = intval($_POST['team_id']);
			
			if(mysql_num_rows(mysql_query("SELECT * FROM guests WHERE id=$guest_id")) == 0) {
				header("Refresh: 2; ./admin.php?page=deltagere");
				die("Gæsten eksisterer ikke");
			}
			
			if(mysql_num_rows(mysql_query("SELECT * FROM teams WHERE id=$team_id")) == 0) {
				header("Refresh: 2; ./admin.php?page=deltagere");
				die("Holdet eksisterer ikke");
			}
			
			ny_deltager($guest_id, $team_id);
			
			header("Location: ./admin.php?page=deltagere");
			
		} else if(isset($_GET['del'])) {
			
			slet_deltager(intval($_GET['del']));
			header("Location: ./admin.php?page=deltagere");
			
		} else {
			$tcontent = "<tr><th>id</th><th>guest_id</th><th>team_id</th><th>pos</th><th>slet</th></tr>";
			
			$query = mysql_query("SELECT * FROM deltagere ". (isset($_POST['query']) ? $_POST['query'] : "ORDER BY id ASC")) or die(mysql_error());
			if(isset($_GET['team_id'])) $query = mysql_query("SELECT * FROM deltagere WHERE team_id=". $_GET['team_id'] ." ORDER BY pos ASC");
			while( $d = mysql_fetch_array($query) ) {
				$team = get_team($d['team_id']);
				$tournament = get_tournament($team['tournament_id']);
				
				$tcontent .= "<tr style='text-align:center;". ($d['pos'] == 0 ? "font-weight:bold;" : "") ."'>
								<td>". $d['id'] ."</td>
								<td><a href='./admin.php?page=guests&id=". $d['guest_id'] ."'>". $d['guest_id'] ."</a></td>
								<td><a href='./admin.php?page=teams&id=". $d['team_id'] ."'>". $d['team_id'] ."</a></td>
								<td style='background-color:". ($d['pos'] == 0 ? ($team['teamstatus'] == "Accepted" ? "green" : "yellow") : "white") ."'>". $d['pos'] ."</td>
								<td>". ($tournament['reg_open'] == 1 ? "<a href='./admin.php?page=deltagere&del=". $d['id'] ."'>X</a>" : "") ."</td>
							</tr>";
			}
			
			echo "<a href='./admin.php'>Back</a><br/><br/>";
			echo "<form action='./admin.php?page=deltagere' method='post'>
						guest_id: <input type='text' name='guest_id' />  team_id: <input type='text' name='team_id' /><input type='submit' value='Ny' />
					</form><br/>";
			echo "Custom query: <form action='./admin.php?page=deltagere' method='post'><span style='background-color:grey'>SELECT * FROM deltagere </span><input type='text' name='query' value='". (isset($_GET['team_id']) ? "WHERE team_id=". $_GET['team_id'] : (isset($_POST['query']) ? $_POST['query'] : "")) ."' /><input type='submit' value='Submit' /></form>";
			echo "Leaders står i bold (pos = 0)<br/>Du kan ikke slette deltagere fra hold i turneringer der allerede er gået i gang.<br/>";
			echo "<table><tbody>". $tcontent ."</tbody></table>";
		}
	} else if($_GET['page'] == "teams") {
		
		if(isset($_GET['del'])) {
			
			slet_hold($_GET['del']);
			header("Location: ./admin.php?page=teams");
			
		} else {
			$tcontent = "<tr><th>id</th><th>navn</th><th>leader_id</th><th>tournament</th><th>bord</th><th>avatar</th><th>spillere</th><th>slet</th></tr>";
			
			$query = mysql_query("SELECT * FROM teams ". (isset($_POST['query']) ? $_POST['query'] : "ORDER BY id ASC")) or die(mysql_error());
			if(isset($_GET['id'])) $query = mysql_query("SELECT * FROM teams WHERE id=". $_GET['id']);
			while( $t = mysql_fetch_array($query) ) {
				$tournament = get_tournament($t['tournament_id']);
				$spillere = mysql_num_rows(mysql_query("SELECT * FROM deltagere WHERE team_id=". $t['id']));
				
				$tcontent .= "<tr style='text-align:center'>
								<td>". $t['id'] ."</td>
								<td style='background-color: ". ($t['teamstatus'] == "Accepted" ? "green" : "yellow") ."'>". $t['navn'] ."</td>
								<td><a href='./admin.php?page=guests&id=". $t['leader_id'] ."'>". $t['leader_id'] ."</a></td>
								<td>(". $t['tournament_id'] .") ". $tournament['navn'] ."</td>
								<td>". $t['bord'] ."</td>
								<td>". ($t['avatarpath'] == "" ? "-" : "<a href='". $t['avatarpath'] ."'>". $t['avatarpath'] ."</a>") ."</td>
								<td><a href='./admin.php?page=deltagere&team_id=". $t['id'] ."'>$spillere</a>/<b>". $tournament['max_spillere'] ."</b></td>
								<td>". ($tournament['reg_open'] == 1 ? "<a href='./admin.php?page=teams&del=". $t['id'] ."'>X</a>" : "") ."</td>
							</tr>";
			}
			
			echo "<a href='./admin.php'>Back</a><br/><br/>";
			echo "Custom query: <form action='./admin.php?page=teams' method='post'><span style='background-color:grey'>SELECT * FROM teams </span><input type='text' name='query' value='". (isset($_GET['id']) ? "WHERE id=". $_GET['id'] : (isset($_POST['query']) ? $_POST['query'] : "")) ."' /><input type='submit' value='Submit' /></form>";
			echo "<table><tbody>". $tcontent ."</tbody></table>";
		}
	} else header("Location: ./admin.php");
	}
	
?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
	a:visited{
		color:blue;
	}
	
	a:hover{
		cursor: pointer;
	}
	
	a{
		color:blue;
		font-weight:normal;
	}
	
	table {
		border-spacing: 10px 5px;
	}
</style>