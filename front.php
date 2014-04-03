<div class="wrapper-midten" id="index-forside">
	  <div id="forside-v">
			<div id="velkommen" align="left">
				<span>Velkommen</span>
			</div>
			<div id="velkommen-tekst">
				<span>
					Igen i år åbner vi dørene op, for en kæmpe weekend med masser af sjov og selvfølgelig hardcore gaming. 

					Vi ønsker alle en god weekend med håbet om et hyggeligt arrangement. 

					Vi forventer at alle holder en sober tone, holder rent omkring sig og ikke mindst, har det fedt!

					Herfra er der ikke så meget mere at sige andet end at vi håber i får det hyggeligt, og få så gamet igennem!<br/><br/>
					
					I år takker vi <a target="_blank" href="http://fcomputer.dk/">Føniks</a> for at være så venlige at sponsorere præmier til os, tjek dem ud!<br/><br/> 
					
					Desuden takker vi <a target="_blank" href="http://dominos.dk/">Domino's</a> for et flot tilbud på pizza-levering fredag aften & lørdag, mere info følger i velkomsttalen og på <a target="_blank" href="https://www.facebook.com/events/632966440097615/">event-siden</a> på facebook.
					Vinderne af CS:GO & LoL-turneringerne kan også glæde sig over gavekort til Domino's restauranter.
				</span>
			</div>
		</div>
		
		<div id="forside-h"> 
			<?php if(!verify_login()) { ?>
				<!-- 		Her er formen til at logge ind med		-->
				<form action="./?p=front" method="post" id="login">
					<label>
						<span>Billetnummer:</span><input type="text" name="billetnr" />
					</label>
						<br />
					<label>
						<span>Password:</span><input type="password" name="pass" />
					</label>
						<br />
					<label>
						<input type="submit" value="Log ind" />
					</label>
				</form>
				
				<!--		Her er formen til at oprette sig med
								når formen submittes, bliver javascript funktionen: checkPass() kørt igennem (functions.js), for at validere de indtastede data -->
				<div>
					<form name="opretForm" onsubmit="return checkPass()" action="./newuser.php" method="post" id="opret">
					<label>
						<span>Billetnummer:</span><input type="text" name="billetnr" />
					</label>
						<br />
					<label>
						<span>Navn:</span><input type="text" name="navn" />
					</label>
						<br />    
					<div id="klasse">
					<span>Klasse:</span>
					<select name="klasse">
						<?php
							$klassearray = get_klasse_array();						
							for($i = 0; $i<sizeof($klassearray); $i++) {echo '<option value="' . $klassearray[$i] . '">' . $klassearray[$i] . '</option>';}
						?>
					</select>
					</div>
						<br />
					<label>
						<span>Password:</span><input type="password" name="pass" />
					</label>
						<br />
					<label>
						<span>Gentag Password:</span><input type="password" name="check_pass" />
					</label>
						<br />        
					<label>
						<input type="submit" value="Opret Bruger" />
					</label>
				</form>
			</div>
		<?php } else { ?>
		
			<div style="width:400px;padding-top: 10px;padding-bottom: 50px;margin-right:40px;">
				<a href="http://dominos.dk/" target="_blank"><img src="./imgs/sponsorer/dominos.png" width="100%" /></a><br/>
				
				<div style="width:375px;margin-top:10px;padding:15px;min-height:200px">
					<span style="font-size: 19px">
						Domino's Pizza leverer direkte til LTG med <div style="font-size:35px;display:inline-block;width:100%;text-align:center">30% rabat!</div><br/><br/> Det sker på disse tidspunkter:<br/>
						<br/>
						<div style="text-align:center;width:100%;font-weight:bold;font-size:30px">
						Fredag kl. 20:30<br/>
						Lørdag kl. 16:00<br/>
						Lørdag kl. 20:30
						</div>
						<br/>
						<span style="font-size: 14px">
							Vil man benytte tilbuddet, skal man senest bestille og betale sin pizza 1 time inden det estimerede leveringstidspunkt. 
							Domino's medarbejdere vil kunne ses i Domino's trøjer igennem hele eventet.<br/>
							<br/>
							Betaling foregår enten kontant, igennem MobilePay eller på kreditkort. Tjek vores menu ud, så ses vi!
						</span>
						<br/>
						<span style="font-size: 11px;">
							Bestilling og betaling skal ske på stedet og ikke via <i>dominos.dk</i>
						</span>
					</span>
				</div>
				
				
				<a href="http://dominos.dk/" target="_blank"><img src="./imgs/sponsorer/dominos.png" width="100%" /></a>
			</div>
	
		<?php } ?>
	</div>
</div>