<?php
	require "login/includes.php";
	
	// Database tjek for at se om navnet er taget
	// Bruges kun af javascript koden længere nede
	if(isset($_POST['tid'])) {
		if(mysql_num_rows(mysql_query("SELECT * FROM teams WHERE tournament_id=". intval($_POST['tid']) ." AND navn='". mysql_real_escape_string($_POST['name'])´´´´´´´´´´´´´´´´´´´´´´´´´´´´´´´´´´´´´´´´´´ ."'")) != 0)
			echo "taken";
		else echo "fine";
		die(mysql_error());
	}
	
	if(!isset($_GET["id"]) || is_nan($_GET["id"])) {
		toIndex();
	}
	
	$klassearray = get_klasse_array();
	$billetnr = íntval($_GET['billetnr']);
	$row = get_guest_wbilletnr($billetnr);


	$user_klasse = $row["klasse"];
	$navn = $row["navn"];
	$userid = $row["id"];


	//tournament id
	$id = intval($_GET["id"]);

	$max_turneringer = mysql_result(mysql_query("SELECT MAX(id) FROM tournaments"),0);

	//Se om spilleren allerede deltager i denne turnering
	$query = mysql_query("SELECT * FROM deltagere WHERE guest_id=$userid");
	while( $deltager = mysql_fetch_array($query) ) {
		$team = get_team($deltager['team_id']);
		if($team['tournament_id'] == $id) {
			toIndex();
		} 
	}



	$arr = mysql_fetch_array(mysql_query("SELECT * FROM tournaments WHERE id=$id"));

	$turnering_navn = $arr["navn"];
	$max_spillere = $arr["max_spillere"];
	$rules = $arr['rules'];
?>
<style>
	.newplayer {
		cursor: pointer;
		color: blue;
	}
</style>
<script type="text/javascript">
	var playerCounter=1;
	var maxSpillere = <?php echo $max_spillere; ?>
	
	var brugere = new Array();
	
	<?php
		// Dette stykke kode opretter et 2dimensionelt array der indeholder alle gæster
		// brugere[klasse][id]
		for($i = 0; $i<sizeof($klassearray); $i++) {
			$c = 0;
			
			echo "brugere['" . $klassearray[$i] . "'] = new Array();\n			";
			
			$query = mysql_query("SELECT * FROM guests WHERE klasse='" . $klassearray[$i] . "' AND billetnr!=$billetnr");
			while($r = mysql_fetch_array($query)) {
				
				echo "brugere['" . $klassearray[$i] . "'][" . $c . "] = '" . $r["navn"] . "-" . $r["id"] . "';\n			";
				$c++;
				
			}
		
		}
		
		
		
	?>
	
	
	
	function newPlayerRow() {
		if(playerCounter==maxSpillere) {
			return;
		}
		document.getElementById("newplayercontainer" + playerCounter).removeChild(document.getElementById("newplayerbtn"));
		playerCounter++;
		document.getElementById("row" + playerCounter).innerHTML = "<td><b>Spiller " + playerCounter + ":</b></td><td><select name='klasse" + playerCounter + "' onchange='klasseValgt(" + playerCounter + ");' ><option value=''></option><?php for($i = 0; $i<sizeof($klassearray); $i++) {echo '<option value=\'' . $klassearray[$i] . '\'>' . $klassearray[$i] . '</option>';} ?> </select></td><td id='namecontainer" + playerCounter + "'></td>";
		if(playerCounter<maxSpillere) {
			document.getElementById("row" + playerCounter).innerHTML = document.getElementById("row" + playerCounter).innerHTML + "<td id='newplayercontainer" + playerCounter + "'><span class='newplayer' onclick='newPlayerRow()' id='newplayerbtn'>[+]</span></td>";
		}
		document.getElementById("playercounter").value = playerCounter;
		
	}
	
	function checkRules() {
		var holdnavn = document.forms["nytHold"]["holdnavn"].value;
		if(holdnavn == "") {alert("Du skal indtaste et holdnavn"); return false;}
	
		// Check name in DB //
		
		// Jeg bruger AJAX til at sende en php request, der tjekker databasen for mig, og ser om navnet allerede er taget
		// (se øverst i denne fil)
		var suc = false
		$.ajax({
			type: "POST",
			url: "join-popup-page.php",
			async: false,
			data: {
				tid: "<?php echo $id; ?>",
				name: document.forms['nytHold']['holdnavn'].value
			},
			success: function(data, textStatus, jqXHR) {
				if(data == "taken") {alert("Navnet er allerede taget.");}
				else if(data == "fine") suc = true;
				else alert(data);
			}
		});
		if(suc == false) return false;
		//////////////////////
	
		var bordnr = document.forms["nytHold"]["bordnr"].value;
		if(isNaN(bordnr)) {alert("Indtast et tal i bordnr."); return false;}
		if(bordnr == "") {alert("Indtast bordnr."); return false;}
		if(bordnr > 16) {alert("Indtast et eksisterende bord."); return false;}
	
		var bool = document.forms["nytHold"]["rulesCheckbox"].checked;
		if(bool==false) {alert("Du skal acceptere reglerne"); return false;}
		
		
		document.getElementById("jsenabled").value="true"; // Gør join.php klar over, at formen blev checket igennem med javascript
		return true;
	}
	
</script>
		<link rel="stylesheet" href="css/turnering-style.css" />
		<div class="join-page" id="outer" style="text-align:center;background-image: url(imgs/tournament-<?php echo $id; ?>-backdrop.png);">
			<div class="join-page" id="middle">
				<div class="join-page" id="inner">
					<span style="font-size:xx-large;">Signup til <?php echo $turnering_navn; ?></span><br />
					<span style="font-size:x-large;"> Lav et nyt hold:</span><br/>
					<?php if($max_spillere > 1) {echo "<span>(Denne turninering kræver ". $max_spillere ." spillere. Du kan invitere alle sammen nu, eller vente til senere)</span>";} ?>
					<br/>
					<form name="nytHold" action="./join.php?id=<?php echo $id; ?>" method="post" onsubmit="return checkRules()" enctype="multipart/form-data" style="display:inline-block;">
					<input type="hidden" value="1" name="playercounter" id="playercounter" />
					<input type="hidden" value="false" name="jsenabled" id="jsenabled" />

						<table>
							<tbody id="signup_body">
								<tr>
									<td>
										<b>Hold navn:</b>
									</td>
									<td>
										<input style="width:100%;height:20px" type="text" name="holdnavn" />
									</td>
									<td>
										<div style="margin-left:50px;display:inline-block;float:left;"> <b>Bordnr:</b> </div> <div style="display:inline-block;margin-left:5px;float:left;"><input style="width:20px;height:20px" type="text" name="bordnr" /></div>
									</td>
								</tr>
								
								<tr>
									<td>
										<b>Rules & Format:</b>
									</td>
									<td style="border: 1px solid black; width: auto; padding:4px;">
										<?php echo $rules; ?>
									</td>
									<td>
										<input type="checkbox" name="rulesCheckbox" value="rules" /> Accept
									</td>
								</tr>
								
								<tr>
									<td>
										<b>Spiller 1:</b>
									</td>
									<td>
										<select name="klasse1" disabled="true">
											<?php
												echo "<option value='" . $user_klasse . "'>" . $user_klasse . "</option>"; //$user_klasse er en variabel som er den klasse som brugeren gaar i.
											?>
										</select>
									</td>
									<td>
										<select name="navn1" disabled="true">
											<?php
												echo "<option value='" . $navn . "'>" . $navn ."</option>"; //$navn er navnet pa brugeren som er logget ind.
											?>
										</select>
									</td>
									
									<td id="newplayercontainer1">
									<?php if($max_spillere != 1) { ?>
										<span class='newplayer' onclick="newPlayerRow()" id='newplayerbtn'>[+]</span>
										<?php } ?>
									</td>
									
								</tr>
								<?php
									for($i=2; $i<=$max_spillere; $i++) {
										echo "<tr id='row" . $i . "'></tr>\n";
									}
									
								?>
								<tr>
									<td>
										<b>Avatar:</a>
									</td>
									<td style="text-align:center;">
										<i>Billedet skaleres kvadratisk<br/>(Maks. 5MB)</i>
									</td>
									<td>
										<input size="1" type="file" name="fileupload" />
									</td>
								</tr>
								<tr>
									<td />
									<td style="text-align:center;">
										<input style="width:100px;margin:auto;" type="submit" value="Registrer!" />
									</td>
								</tr>
								
							</tbody>
						</table>
					</form>
					<?php echo "<div style='display:inline-block;width:100%;margin-top:5px;'><span style='float:left;'><a onclick='$(\"#dialog\").html(\"Loading...\").load(\"tournaments.php\", \"page=team&tid=". $_GET['id'] ."&billetnr=". $_GET['billetnr'] ."\")'>Alle Hold</a></div>"; ?>
				</div>
			</div>
		</div>