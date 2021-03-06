<?php
	if(isset($_POST['edit'])) { // Hvis edit er sat, betyder det at en holdleder forsøger at invitere flere deltagere til sit hold
		//Update hold
		require "login/includes.php";

		$klassearray = get_klasse_array();
		
		$turnering = mysql_fetch_array(mysql_query("SELECT * FROM tournaments WHERE id=". intval($_GET['tid']) ));
		
		$message = "";
		
		
		for($i=2; $i<=$turnering['max_spillere']; $i++) {
			if(isset($_POST['navn'.$i])) {
				if($_POST['navn'.$i]!=0 && $_POST['navn'.$i]!="") {
					//Tjek om der allerede findes en besked til denne spiller
					$query = mysql_query("SELECT * FROM beskeder WHERE modtager_id=". intval($_POST['navn'.$i]));
					$test = 0;
					
					$id = intval($_GET['id']);
					
					$holdnavn = mysql_result(mysql_query("SELECT navn FROM teams WHERE id=$id"),0);
					$leaderid = mysql_result(mysql_query("SELECT leader_id FROM teams WHERE id=$id"),0);
					
					while($row = mysql_fetch_array($query)) {
						if(strpos($row['indhold'], $turnering['navn'])!=false and strpos($row['indhold'], $holdnavn)!=false) {
							$test = 1;
							
							$deltagernavn = mysql_result(mysql_query("SELECT navn FROM guests WHERE id=". intval($_POST['navn'.$i]) ), 0);
							send_message($leaderid, "$deltagernavn blev ikke inviteret, fordi han allerede har et invite liggende eller allerede er medlem af dit hold.", -1);
							$message .= "$deltagernavn blev ikke inviteret, fordi han allerede har et invite liggende eller allerede er medlem af dit hold.<br/>";
						}
						
					}
					
					// Hvis der ikke gør, så kan vi invitere spilleren
					if($test == 0) {
						//Lav en ny invitation
						$hash = md5(time() . rand());
						mysql_query("INSERT INTO invites
									 VALUES('" .  $hash . "', '" . intval($_GET['id']) ."')") or die(mysql_error());
									 
						//send en besked til hver spiller som er inviteret.	
						
						$modtager = intval($_POST["navn" . $i]);
						
						$modtagernavn = mysql_result(mysql_query("SELECT navn FROM guests WHERE id=$modtager"), 0);
						
						
						
						//send_message($leaderid, "Du inviterede $modtagernavn til $holdnavn");
						send_message($modtager, "Du er blevet inviteret til at spille for holdet $holdnavn i ". $turnering['navn'] ."-turneringen. Hvis du ønsker at acceptere, så klik <a href=\'accept_invite.php?hash=" . $hash . "\'>her</a>");	
						send_message($leaderid, "$modtagernavn blev inviteret til $holdnavn.", -1);
						$message .= "$modtagernavn blev inviteret til $holdnavn.<br/>";
					}
				}
			}
		}
		
		header("Location: ./?p=tournaments");
		die($message);
		
	} else if(isset($_POST['del'])) {
		require 'login/includes.php';
		
		// Her slettes der en deltager fra et hold vha en POST-request fra jQuery
		
		$delid = intval($_POST['del']);
		$deltager = get_deltager($delid);
		
		
		$bruger = mysql_fetch_array(mysql_query("SELECT * FROM guests WHERE billetnr=". intval($_SESSION['billetnr']) ));
		$team = get_team($deltager['team_id']);
		
		// Tjek om man har privilegier til at udføre fjernelsen
		if($team['leader_id'] == $bruger['id'] || ($bruger['id'] == $deltager['guest_id'] AND $bruger['id'] != $team['leader_id'])) {
			slet_deltager($deltager['id']);
		} else die("Du er ikke admin for dette hold.");
		
		die("Done.");
	} else if(isset($_GET['delmsg'])) {
		require 'login/includes.php';
	
		// Her slettes der en besked vha en POST-request fra jQuery
	
		mysql_query("DELETE FROM beskeder WHERE id=". intval($_GET['delmsg']) );
		die(mysql_error());
	}
?>

<?php if(!isset($_GET['page'])) { 
	$klassearray = get_klasse_array();
?>


<script type="text/javascript">

	// Denne blanding af javascript og php, lægger alle brugere ind i et multidimensionelt array efter formen: brugere[klasse][nr] = navn-id
	var brugere = new Array();
	<?php
		
		for($i = 0; $i<sizeof($klassearray); $i++) {
			$c = 0;
			
			echo "brugere['" . $klassearray[$i] . "'] = new Array();\n			";
			
			$query = mysql_query("SELECT * FROM guests WHERE klasse='" . $klassearray[$i] . "' AND billetnr!=". intval($_SESSION['billetnr']));
			while($r = mysql_fetch_array($query)) {
				
				echo "brugere['" . $klassearray[$i] . "'][" . $c . "] = '" . $r["navn"] . "-" . $r["id"] . "';\n			";
				$c++;
				
			}
		
		}

	?>
	
	
	function klasseValgt(nr) {
		var txt = "";
		var klasse = document.getElementsByName("klasse" + nr)[0].value;
		for(i=0; i<brugere[klasse].length; i++) {
			var startPos = brugere[klasse][i].indexOf("-");
			var id = brugere[klasse][i].substring(startPos+1);
			var navn = brugere[klasse][i].substring(0, startPos);
			var txt = txt + "<option value='" + id + "'>" + navn + "</option>";
		}
		
		document.getElementById("namecontainer" + nr).innerHTML = "<select style='width:100%' name='navn" + nr + "' ><option value=''></option>" + txt + "</select>";	
	}
	
	$(document).ready(function() {
		// Sætter AJAX op til bl.a. ikke at være asynkront
		$.ajaxSetup({
			cache: false,
			async: false,
			timeout: 10000,
			error: function(X, textStatus, error){
				alert("Error! Check your internet connection.");
			}
		});
	
		$( "#dialog" ).dialog({
			autoOpen: false,
			height: 648,
			width: 960,
			modal: true,
			closeOnEscape: true,
			draggable: false,
			resizable: false,
			show: {
				effect: "fade",
				duration: 400
			},
			close: function() {$( "#dialog ").html("");}
		});
	});
	
	// Åbner dialog div'en med den valgte side
	function createDialog(url, opt) {
		$( "#dialog" ).html("Loading...").load(url, opt);
		
		$( "#dialog" ).dialog( "open" );
	}
	
	// Sletter en besked fra siden (DOM-element) og fra databasen (vha. en post-request)
	function delete_msg(id) {
		var child = document.getElementById(id);
		child.parentNode.removeChild(child);
		
		$.post("tournaments.php?delmsg=" + id);
	}
	
</script>

<div id="dialog">
	Helo
</div>

<?php
		// Viser turneringssiden hvis $_GET['page'] ikke er sat
	
		echo '<div class="wrapper-midten" id="turnering">
            	<div id="wrapper-turnering-alt">
                	<div id="wrapper-turneringsoversigt">
                    	<div id="turnering-overskrift">
                            <p id="t-overskrift" align="center">Turneringer</p>
                        </div>';
	
		$max_turneringer = mysql_result(mysql_query("SELECT MAX(id) FROM tournaments"), 0);
		
		
		// Loopet laver et banner til tilmelding for hver aktive turnering
		$tournament_query = mysql_query("SELECT * FROM tournaments WHERE active=1 ORDER BY id ASC");
		while($tournament = mysql_fetch_array($tournament_query)) {
			$i = $tournament['id'];
			
			$max_spillere = $tournament["max_spillere"];
			
			$billetnr = intval($_SESSION['billetnr']);
			$userid = mysql_result(mysql_query("SELECT id FROM guests WHERE billetnr=$billetnr"), 0);
			
			$besked = '<a onclick="createDialog(\'join-popup-page.php\', \'id='.$i.'&billetnr='.$billetnr.'\');">';
			
			$inteam = false;
			$status = "Pending";
			
			// Tjek om denne gæst allerede er i et hold for denne turnering
			$deltagerquery = mysql_query("SELECT * FROM deltagere WHERE guest_id=$userid");
			while( $deltager = mysql_fetch_array($deltagerquery) ) {
				$team = get_team( $deltager['team_id'] );
				if($team['tournament_id'] == $i) {
				
					$teamid = $team["id"];
					$besked = '<a onclick="createDialog(\'tournaments.php\', \'page=team&tid='.$i.'&id='.$teamid.'&billetnr='. intval($_SESSION['billetnr']) .'\');">';
					$inteam = true;
					$status = mysql_result(mysql_query("SELECT teamstatus FROM teams WHERE id=$teamid"),0);
				
					break;
				}
			}
			
			// Hvis det er for sent at tilmelde sig, og man ikke har noget hold, kan man se alle de tilmeldte hold i stedet
			if(!$inteam && $tournament['reg_open'] == 0) {
				$besked = '<a onclick="createDialog(\'tournaments.php\', \'page=team&tid='.$i.'&billetnr='. intval($_SESSION['billetnr']) .'\');">';
			}
		
		
			echo '<div class="turnerings-billede" id="tournament-' . $i . '" style="'. ($i==0 ? "padding-top:9px !important;" : "")  .'">
					'. $besked .'<img style="width:540px;height:100px;box-shadow: 0px 0px 15px 5px '. ($inteam ? ($status=="Pending" ? 'yellow' : "green") : ($tournament['reg_open'] == 1 ? 'red' : 'gray')) .';" src="./imgs/tournament-'. $i .'.png" alt="'. $tournament['navn'] .'" /></a>
				</div>';
		}
		
		// opretter HTML til at vise beskeder
		echo '</div>
                    <div id="turnering-informationer">
                        <div id="wrapper-turnering-info-overskrift">
                            <div id="turnering-nyheder">
                                <span>Beskeder</span>
                            </div>
                        </div>                       
                        <div class="turnering-info" id="turnering-info">';
						
						
					$bruger = mysql_fetch_array(mysql_query("SELECT * FROM guests WHERE billetnr=". intval($_SESSION['billetnr'])));
					
					
					$query = mysql_query("SELECT * FROM beskeder WHERE modtager_id='" . $bruger['id'] . "' ORDER BY laest ASC, id DESC");
					while($row = mysql_fetch_array($query)) {
						$class = "";
						if($row['laest'] == 0) $class = "unread"; else $class = "read";
					
						$afsender = "System";
						if($row['afsender_id'] != -1) {
							$afsender = get_guest($row['afsender_id']);
							$afsender = $afsender['navn'] . " - " . $afsender['klasse'];
						}
						
						echo "<div class='besked $class' id='". $row['id'] ."'>
								<div class='top'>
									<span style='float:left'>$afsender</span><span style='float:right;font-size:11px'><a onclick='delete_msg(". $row['id'] .");'>x</a></span>
								</div>
								<div class='content'>
									" . $row['indhold'] . "
								</div>
							</div>";
					}
					
					// Fortæl databasen at beskederne nu er sete
					mysql_query("UPDATE beskeder SET laest=1 WHERE modtager_id=". $bruger['id']);
					
						
		echo '			</div>
						<div id="sponsor-logo" hidden>
							<div id="logo-cocio">
								<a href="http://www.cocio.dk/Default.aspx" target="_blank"><img src="imgs/sponsorer/cocio1.jpg" width="inherit" height="107" border="1" alt="Cocio logo" /></a>
							</div>
						</div>
					</div>
					
            	</div>
            </div>
		</div>';
	}
?>

<?php 
	if(isset($_GET['page'])) {
	
	$allowIncludes = true;
	require 'login/includes.php';
	
	if($_GET['page'] == "team") {
		if(!isset($_GET['tid'])) { toIndex(); }
		if(!isset($_GET['billetnr'])) { toIndex(); }
		else {$_GET['billetnr'] = intval($_GET['billetnr']);}
		
		$_GET['tid'] = intval($_GET['tid']);
		
		$klassearray = get_klasse_array();
		$max_players = mysql_result(mysql_query("SELECT max_spillere FROM tournaments WHERE id = ". $_GET['tid']),0);
		
		$bruger = get_guest_wbilletnr($_GET['billetnr']);
		$tournament = get_tournament($_GET['tid']);
		
		?>
		
		<div class="join-page" id="outer" style="text-align:center;background-image: url(imgs/tournament-<?php echo $_GET['tid']; ?>-backdrop.png);">
			<div class="join-page" id="middle">
				<div class="join-page" id="inner">			
					
					<?php
					if(isset($_GET['id'])) { // Hvis id er sat, skal der vises det specifikke hold med den id
						$_GET['id'] = intval($_GET['id']);
						
						$query = mysql_query("SELECT * FROM teams WHERE id = ". $_GET['id']);
						if(mysql_num_rows($query) != 1) { toIndex(); }
						
						$team = get_team($_GET['id']);
						?>
			
						<script src="scripts/functions.js"></script>
						
						<?php
						echo "<span class='text-outline-white' style='font-size:xx-large;'>". $team['navn'] ."</span><br />";
						$status_string = "Bordnr.: <b>". $team['bord'] ."</b><br /> Status: <b><span class='text-outline-black' style='color: ". ($team['teamstatus'] == "Pending" ? "yellow" : "green") .";'>". $team['teamstatus'] ."</b>";
						if($team['avatarpath'] != null) {
							echo "<div style='display:inline-block'><div style='display:inline-block;float:left;'><img style='width:125px;height:125px;' src='". $team['avatarpath'] ."' /></div><div style='display:inline-block;float:left;width:125px;margin-top:2em;margin-left:10px;'>$status_string</div></div>";
						} else echo $status_string;
						
						$tcontent = "";
						$playersinteam = 0;
						
						for($i = 0; $i<$max_players; $i++) {
							$deltagerquery = mysql_query("SELECT * FROM deltagere WHERE team_id=".$team['id']." AND pos=".$i);
							echo mysql_error();
							
							if(mysql_num_rows($deltagerquery)!=1) {
								$tcontent = $tcontent . "<tr><td style='text-align:right;'><b>Spiller ". ($i+1) ."</b></td><td style='padding-left: 10px;'><i>Pending/Not Invited</i></td></tr>";
							} else {
								$deltager = mysql_fetch_array($deltagerquery);
								$player = mysql_fetch_array(mysql_query("SELECT * FROM guests WHERE id=".$deltager['guest_id']));
								echo mysql_error();
								
								if($i==0) {
									$tcontent = $tcontent . "<tr><td style='text-align:right;'><b>Leader</b></td>";
								} else {
									$tcontent = $tcontent . "<tr><td style='text-align:right;'>";
									if((($bruger['id'] == $team['leader_id']) || ($bruger['id'] == $player['id'])) && $tournament['reg_open'] == 1) $tcontent .= "<a onclick='$.post(\"tournaments.php\", { del: \"". $deltager['id'] ."\" });". ($bruger['id'] == $player['id'] ? "window.location=\"./\";" : "createDialog(\"tournaments.php\", \"page=team&tid=". $_GET['tid'] ."&id=". $_GET['id'] ."&billetnr=". intval($_SESSION['billetnr']) ."\");") ."'><span class='delico'>x</span></a>";
									$tcontent .= "<b>Spiller ". ($i+1) ."</b></td>";
								}
								$tcontent = $tcontent . "<td style='display:inline-block;'><span style='padding-left: 10px;". ($player['id'] == $bruger['id'] ? "font-weight:bold;font-style:italic;" : "") ."' id='spiller$i'>". $player['navn'] ."</span><span style='visibility:hidden;float:right;margin-left: 5px;' id='spiller$i"."klasse'><b> // ". $player['klasse'] ."</b></span></td></tr>";
								
								$playersinteam++;
							}
						}
						
						?>
						
						<script type="text/javascript">
							$(document).ready(function() {
							<?php for($i = 0; $i < $playersinteam; ++$i) { ?>
								$( "#spiller<?php echo $i?>" ).hover(function() {
									$( "#spiller<?php echo $i?>klasse" ).css("visibility", "visible");
								}, function() {
									$( "#spiller<?php echo $i?>klasse" ).css("visibility", "hidden");
								});
							<?php } ?>
							});
						</script>
						
						
						<?php
			
						echo "<br/><div id='content' style='display:block;width=100%;'><div style='float:left;display:inline-block;width:100%'><table style='text-align:left;padding: 10px 5px 10px 0;width:100%'>
								<tbody>
									". $tcontent ."
								</tbody>
							</table>";
						$bracketlink = mysql_result(mysql_query("SELECT bracketlink FROM tournaments WHERE id=". $_GET['tid']), 0);
						if($bracketlink != "" && mysql_num_rows(mysql_query("SELECT * FROM teams WHERE tournament_id=". $_GET['tid'] ." AND teamstatus='Accepted'")) >= 2) {
							echo "<a href='#' onclick='$(\"#dialog\").html(\"Loading...\").load(\"tournaments.php\", \"page=bracket&turl=". $bracketlink ."\")'><img height='50px' src='./imgs/brackets.png' /></a>";
						}
						
						if($team['leader_id'] == $bruger['id'] && $team["teamstatus"] != "Pending" && $tournament['reg_open']==1) {
							if($bracketlink != "" && mysql_num_rows(mysql_query("SELECT * FROM teams WHERE tournament_id=". $_GET['tid'])) >= 2) echo "<br/>";
							echo "<a href='./slethold.php?tid=" . $_GET["tid"] . "&id=" . $_GET["id"] . "'><img src='./imgs/slet_hold.png' alt='Slet hold' style='height: 50px;' /></a>";
						}
						echo "</div>";
						
						echo "<br/><div style='display:inline-block;width:100%;margin-top:5px;'><span style='float:left;'><a onclick='$(\"#dialog\").html(\"Loading...\").load(\"tournaments.php\", \"page=team&tid=". $_GET['tid'] ."&billetnr=". $_GET['billetnr'] ."\")'>Alle Hold</a></span>";
						echo ($team['leader_id'] == $bruger['id'] && $team["teamstatus"] == "Pending" ? "<span style='display:inline;float:right'><a onclick='admin_panel(0)'>Admin Panel</a></span>" : "") ."</div>";
						echo "</div>";
						

						// Admin panel skal vises
						//Tjek for at se om brugeren er holdleder
						$bruger = mysql_fetch_array(mysql_query("SELECT * FROM guests WHERE billetnr=". $_GET['billetnr']));
						if($team['leader_id'] == $bruger['id']) {
							if($team['teamstatus']=="Pending") {
								?>
								<script type="text/javascript">
									var html;
									function admin_panel(func) {
										if(func == 0) {
											html = document.getElementById("content").innerHTML;
											
											
											document.getElementById("content").innerHTML = "<?php
											echo "<br/><a href='./slethold.php?tid=" . $_GET["tid"] . "&id=" . $_GET["id"] . "'><img src='./imgs/slet_hold.png' alt='Slet hold' style='height: 50px;' /></a><br/>\ ";
											
											$tcontent = "";
											
											for($i = 2; $i<=$max_players; $i++) {
												$deltagerquery = mysql_query("SELECT * FROM deltagere WHERE team_id=".$team['id']." AND pos=".($i-1));
												if( mysql_num_rows($deltagerquery)==0) {//$row['player'.$i] == 0) {
													$klasser = "";
													for($k = 0; $k<sizeof($klassearray); $k++) {$klasser = $klasser . '<option value=\'' . $klassearray[$k] . '\'>' . $klassearray[$k] . '</option>';}
												
													$tcontent = $tcontent . "<tr> \
														<td><b>Spiller ". $i ."</b></td> \
														<td style='padding-left:10px;'><select name='klasse". $i ."' onchange='klasseValgt(".$i.");'><option value=''></option>". $klasser ."</select></td> \
														<td id='namecontainer". $i ."'></td> \
													</tr>\ ";
												}
											}
									
											echo "<form name='editHold' action='./tournaments.php?tid=".$_GET['tid']."&id=".$_GET['id']."' method='post' style='display:inline-block;margin-top:10px;margin-bottom:10px;'> \
													<span style='font-size:x-large;padding: 0 10px;'>Tilføj resten af spillerne her:</span> \
													<input type='hidden' value='1' name='edit' /> \
													<table style='text-align:left;'> \
														<tbody> \
															". $tcontent ." \
														</tbody> \
													</table> \
													<input style='width:50%;margin:auto' type='submit' value='Inviter spillere!' /> \
												</form><br /><span style='display:inline-block;width:100%;text-align:right;'><a onclick='admin_panel(1)'>Back</a></span>";
											?>";
											
											
										} else {
											document.getElementById("content").innerHTML = html;
											
											<?php for($i = 0; $i < $playersinteam; ++$i) { ?>
												$( "#spiller<?php echo $i?>" ).hover(function() {
													$( "#spiller<?php echo $i?>klasse" ).css("visibility", "visible");
												}, function() {
													$( "#spiller<?php echo $i?>klasse" ).css("visibility", "hidden");
												});
											<?php } ?>
										}
									}
								
								</script>
								<?php
							}
						}

					} else { // når id ikke er sat
						//rems alle hold op
						$query = mysql_query("SELECT * FROM teams WHERE tournament_id=". $_GET['tid'] ." ORDER BY seed ASC,id ASC");
						
						$tcontent = "";
						while($row = mysql_fetch_array($query)) {
							$leader = mysql_fetch_array(mysql_query("SELECT * from guests WHERE id=". $row['leader_id']));
						
							$tcontent = $tcontent . "<div class='team'>
														<span><a onclick='$(\"#dialog\").html(\"Loading...\").load(\"tournaments.php\", \"page=team&tid=". $_GET['tid'] ."&id=". $row['id'] ."&billetnr=". $_GET['billetnr'] ."\")'>". $row['navn'] ."</a></span><br/>
														<span style='font-size:0.8em'>Bord: ". $row['bord'] ." - <b>". $leader['klasse'] ."</b></span>
													</div>";
						}
						
						if(mysql_num_rows($query)==0) {
							$tcontent = "<tr>
											<td>Der er endnu ingen hold tilmeldt til denne turnering.</td>
										</tr>";
						}
						
						$turnering_navn = mysql_result(mysql_query("SELECT navn FROM tournaments WHERE id=". $_GET['tid']), 0);
						
						echo "<span style='font-size:2em;' class='text-blur-white'>". $turnering_navn ."</span><br/>
							
							<div id='team-container'>
									". $tcontent ."
							</div><br/>";
							
							// Lad spilleren signe op hvis han ikke er tilmeldt:
							$query = mysql_query("SELECT * FROM deltagere WHERE guest_id=". $bruger['id']);
							$signedup = false;
							while( $deltager = mysql_fetch_array($query) ) {
								$team = get_team($deltager['team_id']);
								if($team['tournament_id'] == $_GET['tid']) {
									$signedup = true;
									break;
								}
							}
							if(!$signedup && $tournament['reg_open'] == 1) echo "<div style='display:inline-block;width:100%;margin-top:5px;'><span style='float:left;'><a onclick='$(\"#dialog\").html(\"Loading...\").load(\"join-popup-page.php\", \"id=". $_GET['tid'] ."&billetnr=". $_GET['billetnr'] ."\")'>Signup</a></div>";
					}
					?>
				</div>
			</div>
		</div>			
					
		<?php
		
		
	} else if($_GET["page"] == "bracket") {
		if(!isset($_GET["turl"])) { toIndex(); }
		$challonge_url = $_GET["turl"];
		
		?>
		
		<div style="width:100%;height:100%;" id="bracket_div"></div>
		<script src="./scripts/jquery.challonge.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#bracket_div').challonge('<?php echo $challonge_url; ?>', {subdomain: '', theme: '1', multiplier: '0.8', match_width_multiplier: '0.8', show_final_results: '0', show_standings: '0'});
			});
		</script>
		
		<?php	
	} else {toIndex();}
}
?>