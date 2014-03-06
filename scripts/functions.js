

// Checks data on registration form on front page
function checkPass() {
	var bnr = document.forms["opretForm"]["billetnr"].value;
	if(isNaN(bnr)) {alert("Dit billetnummer er ugyldigt."); return false;}
	if(bnr.toString().length != 6) {alert("Dit billetnummer er altså 6 cifre langt."); return false;}
	
	var navn = document.forms["opretForm"]["navn"].value;
	if((navn.length) > 30) {alert("Dit navn er for langt, det må højst være på 30 bogstaver."); return false;}
	if(navn == "") {alert("Skriv dit navn"); return false;}

	var x = document.forms["opretForm"]["pass"].value;
	var y = document.forms["opretForm"]["check_pass"].value;
	
	if(x!=y || ((x=="" || x==null) || (y=="" || y==null))) {alert("Dine passwords er ikke ens!"); return false;}
}


// Changes the sponsor regularly in the header
function changeSponsor() {
	if(state == 0) {
		document.getElementById("logo-js").innerHTML = '<a href="http://www.dominos.dk/" target="_blank"><img src="imgs/sponsorer/dominos.png" height="80px" border="1" alt="Dominos" /></a>';
		state = 2; // NB: hopper til føniks, unqhosting er nede
	} else if(state == 1) {
		document.getElementById("logo-js").innerHTML = '<a href="http://unqhosting.com/" target="_blank"><img src="imgs/sponsorer/unqhosting.png" height="80px" border="1" alt="UNQHosting" /></a>';
		state = 2;
	} else {
		document.getElementById("logo-js").innerHTML = '<a href="http://fcomputer.dk/" target="_blank"><img src="imgs/sponsorer/foeniks.png" height="80px" border="1" alt="Føniks" /></a>';
		state = 0;
	}
}