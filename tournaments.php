<?php
	if(isset($_POST['edit'])) {
		//Update hold
		require "login/includes.php";

		$klassearray = get_klasse_array();
		
		$turnering = mysql_fetch_array(mysql_query("SELECT * FROM tournaments WHERE id=".$_GET['tid']));
		
		
		
		for($i=2; $i<=$turnering['max_spillere']; $i++) {
			if(isset($_POST['navn'.$i])) {
				if($_POST['navn'.$i]!=0 && $_POST['navn'.$i]!="") {
					//Tjek om der allerede findes en besked til denne spiller
					$query = mysql_query("SELECT * FROM beskeder WHERE modtager_id=".$_POST['navn'.$i]);
					$test = 0;
					
					$holdnavn = mysql_result(mysql_query("SELECT navn FROM teams WHERE id='". $_GET['id']."'"),0);
					$leaderid = mysql_result(mysql_query("SELECT leader_id FROM teams WHERE id=". $_GET['id']),0);
					
					while($row = mysql_fetch_array($query)) {
						if(strpos($row['indhold'], $turnering['navn'])!=false and strpos($row['indhold'], $holdnavn)!=false) {
							$test = 1;
							
							$deltagernavn = mysql_result(mysql_query("SELECT navn FROM guests WHERE id=". $_POST['navn'.$i]), 0);
							echo "$deltagernavn blev ikke inviteret, fordi han allerede har et invite liggende eller allerede er medlem af dit hold.<br/>";
						}
						
					}
					
					if($test == 0) {
						//Lav en ny invitation
						$hash = md5(time() . rand());
						mysql_query("INSERT INTO invites
									 VALUES('" .  $hash . "', '" . $_GET['tid'] . "', '" . $_GET['id'] . "')");
									 
						//send en besked til hver spiller som er inviteret.	
						
						$max_id = mysql_query("SELECT MAX(id) FROM beskeder");
						$max_id = mysql_result($max_id,0);
						$max_id = $max_id+1;
						
						$modtager = $_POST["navn" . $i];
						$modtager = mysql_real_escape_string($modtager);
						
						$modtagernavn = mysql_result(mysql_query("SELECT navn FROM guests WHERE id=$modtager"), 0);
						
						
						
						send_message($leaderid, "Du inviterede $modtagernavn til $holdnavn");
						send_message($modtager, "Du er blevet inviteret til at spille for holdet $holdnavn i ". $turnering['navn'] ."-turneringen. Hvis du ønsker at acceptere, så klik <a href=\'accept_invite.php?hash=" . $hash . "\'>her</a>");	
						echo "$modtagernavn blev inviteret til $holdnavn.<br/>";
					}
				}
			}
		}
		
		header("refresh: 2; ./");
		exit;
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
			
			$query = mysql_query("SELECT * FROM guests WHERE klasse='" . $klassearray[$i] . "' AND billetnr!='". $_SESSION['billetnr'] ."'");
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
		$.ajaxSetup({
			cache: false,
			async: false,
			timeout: 10000,
			error: function(X, textStatus, error){
				alert("error");
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
			hide: {
				effect: "puff",
				duration: 200
			},
			close: function() {document.getElementById("dialog").innerHTML = "";}
		});
	});
	
	
	function createDialog(url, opt) {
		$( "#dialog" ).html("Loading...").load(url, opt);
		
		$( "#dialog" ).dialog( "open" );
	}
	
</script>

<div id="dialog">
	Helo
</div>

<?php
	
		echo '<div class="wrapper-midten" id="turnering">
            	<div id="wrapper-turnering-alt">
                	<div id="wrapper-turneringsoversigt">
                    	<div id="turnering-overskrift">
                            <p id="t-overskrift" align="center">Turneringer</p>
                        </div>';
	
		$max_turneringer = mysql_query("SELECT MAX(id) FROM tournaments");
		$max_turneringer = mysql_result($max_turneringer,0);
		
		$tournament_query = mysql_query("SELECT * FROM tournaments WHERE active=1 ORDER BY id ASC");
		
		while($tournament = mysql_fetch_array($tournament_query)) {
			$i = $tournament['id'];
			
			$max_spillere = $tournament["max_spillere"];
			
			$billetnr = $_SESSION['billetnr'];
			$row = mysql_fetch_array(mysql_query("SELECT * FROM guests WHERE billetnr='$billetnr'"));
			$userid = $row["id"];
			
			//$besked = '<a href="./?p=tournaments&page=join&id=' . $i . '">';
			$besked = '<a onclick="createDialog(\'join-popup-page.php\', \'id='.$i.'&billetnr='.$billetnr.'\');">';
			
			$inteam = false;
			$status = "Pending";
					
			$deltagerquery = mysql_query("SELECT * FROM deltagere WHERE guest_id='$userid' and tournament_id=$i");
			if(mysql_num_rows($deltagerquery)>=1) {
				$deltager = mysql_fetch_array($deltagerquery);
				
				$teamid = $deltager["team_id"];
				//$besked = '<a href="./?p=tournaments&page=team&tid=' . $i . '&id=' . $teamid . '">';
				$besked = '<a onclick="createDialog(\'tournaments.php\', \'page=team&tid='.$i.'&id='.$teamid.'&billetnr='. $_SESSION['billetnr'] .'\');">';
				$inteam = true;
				$status = mysql_result(mysql_query("SELECT teamstatus FROM teams WHERE id=$teamid"),0);
			}
		
		
			echo '<div class="turnerings-billede" id="tournament-' . $i . '" style="'. ($i==0 ? "padding-top:9px !important;" : "")  .'">
					'. $besked .'<img style="width:540px;height:100px;box-shadow: 0px 0px 15px 5px '. ($inteam ? ($status=="Pending" ? 'yellow' : "green") : 'red') .';" src="./imgs/tournament-'. $i .'.png" alt="'. $tournament['navn'] .'" /></a>
				</div>';
		}
		
		echo '</div>
                    <div id="turnering-informationer">
                        <div id="wrapper-turnering-info-overskrift">
                            <div id="turnering-nyheder">
                                <span>Beskeder</span>
                            </div>
                        </div>                       
                        <div class="turnering-info">';
						
						
					if(verify_login()) {
						$bruger = mysql_fetch_array(mysql_query("SELECT * FROM guests WHERE billetnr='". $_SESSION['billetnr'] ."'"));
						
						
						$query = mysql_query("SELECT * FROM beskeder WHERE modtager_id='" . $bruger['id'] . "' ORDER BY laest ASC");
						while($row = mysql_fetch_array($query)) {
							$class = "";
							if($row['laest'] == 0) {$class = "besked-unread";} else {$class = "besked-read";}
							
							echo "<div class='". $class ."'>
									" . $row['indhold'] . "
								</div><br />";
								
							mysql_query("UPDATE beskeder SET laest=1
										WHERE id=". $row['id']);
						}
					}
						
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
	require 'login/connect.php';
	require 'login/functions.php';
	
	if($_GET['page'] == "team") {
		if(!isset($_GET['tid'])) {header("Location: ./");}
		if(!isset($_GET['billetnr'])) {header("Location: ./");}
		else {$_GET['billetnr'] = mysql_real_escape_string($_GET['billetnr']);}
		
		$_GET['tid'] = mysql_real_escape_string($_GET['tid']);
		
		$klassearray = get_klasse_array();
		$max_players = mysql_result(mysql_query("SELECT max_spillere FROM tournaments WHERE id = ". $_GET['tid']),0);
		
		$bruger = mysql_fetch_array(mysql_query("SELECT * FROM guests WHERE billetnr=". $_GET['billetnr']));
		
		?>
		
		<div class="join-page" id="outer" style="text-align:center;background-image: url(imgs/tournament-<?php echo $_GET['tid']; ?>-backdrop.png);">
			<div class="join-page" id="middle">
				<div class="join-page" id="inner">			
					
					<?php
					if($max_players > 1) {
						if(isset($_GET['id'])) {
							$_GET['id'] = mysql_real_escape_string($_GET['id']);
							
							$query = mysql_query("SELECT * FROM teams WHERE id = ". $_GET['id']);
							if(mysql_num_rows($query) != 1) {header("Location: ./");}
							
							$team = mysql_fetch_array($query);
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
										$tcontent = $tcontent . "<tr><td style='text-align:right;'><b>Spiller ". ($i+1) ."</b></td>";
									}
									$tcontent = $tcontent . "<td><span style='padding-left: 10px;". ($player['id'] == $bruger['id'] ? "font-weight:bold;font-style:italic;" : "") ."' id='spiller$i'>". $player['navn'] ."</span><span style='visibility:hidden;float:right;margin-left: 5px;' id='spiller$i"."klasse'><b> // ". $player['klasse'] ."</b></span></td></tr>";
									
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
							if($bracketlink != "") {
								echo "<a href='#' onclick='$(\"#dialog\").html(\"Loading...\").load(\"tournaments.php\", \"page=bracket&turl=". $bracketlink ."\")'><img height='50px' src='./imgs/brackets.png' /></a>";
							}
							
							if($team['leader_id'] == $bruger['id'] && $team["teamstatus"] != "Pending") {
								if($bracketlink != "") echo "<br/>";
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
													</form><br/><a href='./slethold.php?tid=" . $_GET["tid"] . "&id=" . $_GET["id"] . "'><img src='./imgs/slet_hold.png' alt='Slet hold' style='height: 50px;' /></a><br /><span style='display:inline-block;width:100%;text-align:right;'><a onclick='admin_panel(1)'>Back</a></span>";
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

						} else {
							//rems alle hold op
							$query = mysql_query("SELECT * FROM teams WHERE tournament_id=". $_GET['tid'] ." ORDER BY id DESC");
							
							$tcontent = "";
							while($row = mysql_fetch_array($query)) {
								$tcontent = $tcontent . "<tr>
															<td><a onclick='$(\"#dialog\").html(\"Loading...\").load(\"tournaments.php\", \"page=team&tid=". $_GET['tid'] ."&id=". $row['id'] ."&billetnr=". $_GET['billetnr'] ."\")'><span class='teamname'>". $row['navn'] ."</span></a></td>
														</tr>";
							}
							
							if(mysql_num_rows($query)==0) {
								$tcontent = "<tr>
												<td>Der er endnu ingen hold tilmeldt til denne turnering.</td>
											</tr>";
							}
							
							echo "<div><table style='text-align:left;float:left;margin-right:30px;'>
									<tbody>
										". $tcontent ."
									</tbody>
								</table></div>";
						}
					} else {
						//Rems alle spillere op (til 1-mands turneringer)
						$query = mysql_query("SELECT * FROM teams WHERE tournament_id=". $_GET['tid'] ." ORDER BY id DESC");
							$tcontent = "";
							while($row = mysql_fetch_array($query)) {
								$player = mysql_fetch_array(mysql_query("SELECT * FROM guests WHERE id=". $row['leader_id']));
								$tcontent = $tcontent . "<tr>
															<td><span class='teamname'>". $row['navn'] ."</span></td><td>". $player['klasse'] ."</td>
														</tr>";
							}
							
						echo "<div><table style='text-align:left;float:left;margin-right:30px;'>
								<tbody>
									". $tcontent ."
								</tbody>
							</table></div>";
					}
					?>
				</div>
			</div>
		</div>			
					
		<?php
		
		
	} else if($_GET["page"] == "bracket") {
		?> <script src="https://raw.github.com/challonge/challonge-jquery-plugin/master/jquery.challonge.js"></script> <?php
		$challonge_url = $_GET["turl"];
		echo '<iframe src="'. $challonge_url .'/module" style="width:925px;height:580px;margin:0px;" frameborder="0" scrolling="auto" allowtransparency="true"></iframe>';
		
	} else {header("Location: ./");}
}
?>