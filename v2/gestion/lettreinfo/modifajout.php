<?php
/*****************************************************
Modification d'un utilisateur
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Affichage du message s'il existe
$messager = $_SESSION['message'];
unset($_SESSION['message']);

// Détermination de l'id
if (isset($_GET['id'])) $id_lettre = (int)$_GET['id'];
$mode_ajout = (bool)$_GET['ajout'];

if($id_lettre||$mode_ajout) {

    if ($mode_ajout) {
	$titrepage = "Ajout d'$cc_une";
	$id_lettre = 0;
    }
    else
    {
	$titrepage = "Modification d'$cc_une";
    }
    
    $requete = "SELECT territoires_id FROM lettreinfo WHERE no=".$id_lettre;
    $res_requete = $connexion->prepare($requete);
    $res_requete->execute() or die("erreur requête ligne 116 : ".$requete);
    $tabter = $res_requete->fetch();
    $territoire = $tabter['territoires_id'];
     
  // Lignes à ajouter au header
	$cc_cettemin_slash = addslashes($cc_cettemin);
    $ajout_header = <<<AJHE
<script type="text/javascript" src="../../js/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../js/fonction_auto_presentation.js"></script>
<script type="text/javascript" src="_f.js"></script>
<script type="text/javascript">
	var Y_MENU_DEPART;
	var ANCRE_COURANTE;
	var OBJET_MODIFICATION;
	var DATE_MODIFICATION;
	function getXhr(){if(window.XMLHttpRequest) xhr=new XMLHttpRequest();else if(window.ActiveXObject){try{xhr=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){xhr=new ActiveXObject("Microsoft.XMLHTTP");}}else{alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest, veuillez le mettre Ã  jour");xhr=false;}return xhr;}
	function ecran(){if (document.body){var larg=(document.body.clientWidth);var haut=(document.body.clientHeight);}else{var larg=(window.innerWidth);var haut=(window.innerHeight);}return {"x":larg,"y":haut};}
	function vide(e){if(e.firstChild){e.removeChild(e.firstChild);vide(e);}}
  window.onload = function()
  {
	Y_MENU_DEPART = document.getElementById("zone_avancement").offsetTop;
	position_menu();
	  $("#liste_repertoire").load("affrepertoire.php", {id:$id_lettre});
	  $("#liste_petiteannonce").load("affpetiteannonce.php", {id:$id_lettre});
	  $("#liste_agenda").load("affagenda.php", {id:$id_lettre});
	  $("#liste_edito").load("affedito.php", {id:$id_lettre});
	  $("#zone_partenaire_institutionnel").load("affpartenaireinstitutionnel.php", {id:$id_lettre,territoire:$territoire});
	  $("#zone_publicite").load("affpublicite.php", {id:$id_lettre});
	  document.body.setAttribute("onscroll","position_menu();");
	   // document.body.onscroll = position_menu;
	   // window.onscroll = position_menu;
	   // if(document.documentElement)
		// document.documentElement.onscroll = function(){position_menu()};
	   // window.onscroll = function(){position_menu()};
	   // document.body.onhashchange = function(){position_aide()}
	  document.body.setAttribute("onhashchange","position_aide();");
		ANCRE_COURANTE = position_aide();
  };
	function position_aide(a){
		//On récupère l'ancre
		if(typeof(a)=="undefined"){
			if(window.location.hash!="")
				a = window.location.hash.split("_")[1];
			else
				a = "generalites";
		}
		if(a=="generalites")
			document.getElementById("btn_aide").style.left = 0+"px";
		else if(a=="edito")
			document.getElementById("btn_aide").style.left = 110+"px";
		else if(a=="agenda")
			document.getElementById("btn_aide").style.left = 220+"px";
		else if(a=="repertoire")
			document.getElementById("btn_aide").style.left = 330+"px";
		else if(a=="petiteannonce")
			document.getElementById("btn_aide").style.left = 440+"px";
		return a;
	}
	$(function() {
   
	    $('#ajax-supp').hide();
    
	    // Confirmation suppression 
	    $('.delete-evtstruct').click(function(){
	      var answer = confirm('Etes-vous sur de vouloir supprimer $cc_cettemin_slash et toutes ses associations ?');
	      return answer;
	    });

	});
	
	 // colorbox sur .iframe
	$("#ajoutrepertoire").live('click', function() {
		$.fn.colorbox({
		  href:"ajoutrepertoire.php?no_lettre=$id_lettre",
		  width:"550px",
		  onClosed:function(){
			  $("#liste_repertoire").load("affrepertoire.php", {id:$id_lettre});
			  $(".message").load("../inc-message.php");
		  },
		  onComplete : function() { 
				$(this).colorbox.resize();
		  }
		});
		return false; 
	});
	
	$("#ajoutpetiteannonce").live('click', function() {
		$.fn.colorbox({
		  href:"ajoutpetiteannonce.php?no_lettre=$id_lettre",
		  width:"550px",
		  onClosed:function(){
			  $("#liste_petiteannonce").load("affpetiteannonce.php", {id:$id_lettre});
			  $(".message").load("../inc-message.php");
		  },
		  onComplete : function() { 
				$(this).colorbox.resize();
		  }
		});
		return false; 
	});
	$("#boitepetiteannonce").live('click', function() {
		$.fn.colorbox({
		  href:"boite_petiteannonce.php?id=$id_lettre",
		  width:"550px",
		  onClosed:function(){
			  $("#liste_petiteannonce").load("affpetiteannonce.php", {id:$id_lettre});
			  $(".message").load("../inc-message.php");
		  },
		  onComplete : function() { 
				$(this).colorbox.resize();
		  }
		});
		return false; 
	});
	
	$("#ajoutagenda").live('click', function() {
		$.fn.colorbox({
		  href:"ajoutagenda.php?no_lettre=$id_lettre",
		  width:"550px",
		  onClosed:function(){
			  $("#liste_agenda").load("affagenda.php", {id:$id_lettre});
			  $(".message").load("../inc-message.php");
		  },
		  onComplete : function() { 
				$(this).colorbox.resize();
		  }
		});
		return false; 
	});
	
	$("#ajoutagenda_edito").live('click', function() {
		$.fn.colorbox({
		  href:"ajoutagenda_edito.php?no_lettre=$id_lettre",
		  width:"550px",
		  onClosed:function(){
			  // $("#liste_agenda").load("affagenda.php", {id:$id_lettre});
			  $(".message").load("../inc-message.php");
		  },
		  onComplete : function() { 
				$(this).colorbox.resize();
		  }
		});
		return false; 
	});
	$("#ajoutpetiteannonce_edito").live('click', function() {
		$.fn.colorbox({
		  href:"ajoutpetiteannonce_edito.php?no_lettre=$id_lettre",
		  width:"550px",
		  onClosed:function(){
			  // $("#liste_agenda").load("affagenda.php", {id:$id_lettre});
			  $(".message").load("../inc-message.php");
		  },
		  onComplete : function() { 
				$(this).colorbox.resize();
		  }
		});
		return false; 
	});
	$("#ajoutrepertoire_edito").live('click', function() {
		$.fn.colorbox({
		  href:"ajoutrepertoire_edito.php?no_lettre=$id_lettre",
		  width:"550px",
		  onClosed:function(){
			  // $("#liste_agenda").load("affagenda.php", {id:$id_lettre});
			  $(".message").load("../inc-message.php");
		  },
		  onComplete : function() { 
				$(this).colorbox.resize();
		  }
		});
		return false; 
	});
	
	$("#boiteagenda").live('click', function() {
		$.fn.colorbox({
		  href:"boite_agenda.php?id=$id_lettre",
		  width:"550px",
		  onClosed:function(){
			  $("#liste_agenda").load("affagenda.php", {id:$id_lettre});
			  $(".message").load("../inc-message.php");
		  },
		  onComplete : function() { 
				$(this).colorbox.resize();
		  }
		});
		return false; 
	});
	
	$("#modif_edito").live('click', function() {
		$.fn.colorbox({
		  href:"mention_permanente.php?id=$id_lettre&territoire=$territoire",
		  width:"550px",
		  onClosed:function(){
				// if(CKEDITOR.instances["mention_permanente"])
					// CKEDITOR.instances["mention_permanente"].destroy();
				// if(CKEDITOR.instances["edito"])
					// CKEDITOR.instances["edito"].destroy();
				// if (CKEDITOR.instances['edito']) {
						// CKEDITOR.remove(CKEDITOR.instances['edito']);
				// }
				if(CKEDITOR.instances["mention_permanente"])
					CKEDITOR.instances["mention_permanente"].destroy();
			  $("#liste_edito").load("affedito.php", {id:$id_lettre,territoire:$territoire});
			  $(".message").load("../inc-message.php");
		  },
		  onComplete : function() { 
				CKEDITOR.replace('mention_permanente',{toolbar:'NewsLetter',uiColor:'#F0EDEA',language:'fr',height:'300',skin:'kama',enterMode : CKEDITOR.ENTER_BR});
		  		$(this).colorbox.resize();
		  }
		});
		return false; 
	});
	
	$("#btn_previsualisation").live('click', function() {
		$.fn.colorbox({
		  href:"lettre_en_cours/index.php?no=$id_lettre",
		  width:"770px",
		  onComplete : function() { 
				$(this).colorbox.resize();
		  }
		});
		return false; 
	});
	
	$("#btn_aide").live('click', function() {
		$.fn.colorbox({
		  href:"aide.php?no_lettre=$id_lettre&e="+escape(ANCRE_COURANTE),
		  width:"770px",
		  onComplete : function() { 
				$(this).colorbox.resize();
		  }
		});
		return false; 
	});
	
	function placePhotoDansCadre(photo,cadre){
		cadre.style.position = "relative";
		photo.style.position = "absolute";
		var hauteurPhoto = photo.offsetHeight;
		var largeurPhoto = photo.offsetWidth;
		var hauteurCadre = cadre.offsetHeight;
		var largeurCadre = cadre.offsetWidth;
		var rapportHauteur = hauteurCadre/hauteurPhoto;
		var rapportLargeur = largeurCadre/largeurPhoto;
		if(rapportHauteur>1){
			//L'image est alors plus petite que le cadre sur la hauteur:
				//On ne l'étire pas, on la centre.
			photo.style.top = hauteurCadre/2 - hauteurPhoto/2+"px";
			photo.style.left = largeurCadre/2 - largeurPhoto/2+"px";
			//On règle maintenant la largeur
			/*if(rapportLargeur>1){
				//L'image est alors plus large que le cadre
					//On la rogne sur la largeur
				photo.style.left = largeurCadre/2 - largeurPhoto/2+"px";
			}
			else if (rapportLargeur<1){
				//L'image est moins large que le cadre
					//On la centre sur la largeur
					photo.style.left = largeurCadre/2 - largeurPhoto/2+"px";
			}
			else{
				
			}*/
		}
		else{
			//L'image est alors plus grande que le cadre sur la hauteur:
				//On peut alors selon la largeur la rogner, ou la retrecir sur la hauteur
				if(rapportHauteur>rapportLargeur){
					//L'image est alors plus large que longue:
						//On la retreci sur la hauteur puis on la rogne sur la largeur
					photo.style.height = hauteurCadre+"px";
					largeurPhoto = photo.offsetWidth;
					photo.style.left = largeurCadre/2 - largeurPhoto/2+"px";
					photo.style.top = 0+"px";
				}
				else if(rapportHauteur<rapportLargeur){
					//L'image est alors plus haute que large:
						//On la retreci sur la largeur puis on la rogne sur la hauteur
					photo.style.width = largeurCadre+"px";
					hauteurPhoto = photo.offsetHeight;
					photo.style.top = hauteurCadre/2 - hauteurPhoto/2+"px";
					photo.style.left = 0+"px";
				}
				else{
					//L'image est a le même rapport que le cadre:
						//On la retreci à la taille du cadre.
						photo.style.height = hauteurCadre+"px";
						photo.style.width = largeurCadre+"px";
						photo.style.top = 0+"px";
						photo.style.left = 0+"px";
				}
		}
		photo.style.visibility = "visible";
		cadre.style.backgroundColor = "transparent";
		cadre.style.overflow = "hidden";
	}
	function position_menu(){
		var scroll_top = scroll()["y"];
		if(scroll_top>Y_MENU_DEPART-7){
			verif_scroll_etape(scroll_top);
			//On modifie la position du zone_avancement
			document.getElementById("zone_avancement").style.top = (scroll_top-Y_MENU_DEPART)+7+"px";
			// style("zone_avancement","top",); //relative
			// style("zone_avancement","top",(scroll_top)+"px"); //absolute
		}
		else{
			if(document.getElementById("zone_avancement").offsetTop>Y_MENU_DEPART){
				document.getElementById("zone_avancement").style.top = 0+"px";
			}
		}
	}
		function scroll(){
			return {"x":(document.all ? document.scrollLeft : window.pageXOffset),"y":(document.all ? document.scrollTop : window.pageYOffset)}
		}
		
		function verif_scroll_etape(scroll_top){
			// var scroll_top = scroll()["y"];
			// alert((scroll_top-Y_MENU_DEPART)+7);
			
			if(scroll_top>document.getElementById("filed_petiteannonce").offsetTop)
				var a = "petiteannonce";
			else if(scroll_top>document.getElementById("filed_repertoire").offsetTop)
				var a = "repertoire";
			else if(scroll_top>document.getElementById("filed_agenda").offsetTop)
				var a = "agenda";
			else if(scroll_top>document.getElementById("filed_edito").offsetTop)
				var a = "edito";
			else
				var a = "generalites";
			if(a!=ANCRE_COURANTE){
				// alert(a+" -> "+scroll_top+" : "+document.getElementById("filed_petiteannonce").offsetTop);
				ANCRE_COURANTE = position_aide(a);
			}
			// alert(document.getElementById("filed_generalites").offsetTop);
			// alert(document.getElementById("filed_edito").offsetTop);
			// alert(document.getElementById("filed_agenda").offsetTop);
			// alert(document.getElementById("filed_repertoire").offsetTop);
			// alert(document.getElementById("filed_petiteannonce").offsetTop);
			
		}

var TEXTE_ZONE = {"petiteannonce":["petite annonce","petites annonces"],"repertoire":["structure","structures"],"structure":["structure","structures"],"agenda":["événement","événements"]};
function verif_check(input){
	input.checked = !input.checked;
	if(input.checked){
		input.nextSibling.nextSibling.firstChild.data = "retirer";
		input.parentNode.parentNode.style.boxShadow = "0px 0px 8px #96DF5F";
	}
	else{
		input.nextSibling.nextSibling.firstChild.data = "ajouter";
		input.parentNode.parentNode.style.boxShadow = "0px 0px 8px #aaa";
	}
	var div = input.parentNode.parentNode;
	var contenaire = div.parentNode;
	var zone = contenaire.id.split("_")[0];
	// var nb_max = 5;
	var nb_cour = compte_nb_check(div.parentNode);
	if(zone!="agenda"){
		document.getElementById("valider_"+zone).style.visibility = "visible";
		message(zone,"Vous pouvez valider l'&eacute;tape.",0);
		var liste = recuperer_liste_check(div.parentNode);
		var xhr = getXhr();
			xhr.open("POST", "ajax/modif_liste.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send("e="+escape(zone)+"&l="+escape(liste)+"&no_l=$id_lettre");
		var reponse = eval("("+xhr.responseText+")");
		if(!reponse)
			alert("une erreur s'est produite");
	}
	else{
		var liste = recuperer_liste_check(div.parentNode);
		var xhr = getXhr();
			xhr.open("POST", "ajax/modif_liste.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send("e="+escape(zone)+"&l="+escape(liste)+"&no_l=$id_lettre");
		var reponse = eval("("+xhr.responseText+")");
		if(!reponse)
			alert("une erreur s'est produite");
	}
	
}
	function compte_nb_check(div){
		if(typeof(div)=="string")
			div = document.getElementById(div);
		les_inputs = div.getElementsByTagName("input");
		var nb_check = 0;
		for(i=0;i<les_inputs.length;i++){
			if(les_inputs[i].getAttribute("type")=="checkbox"){
				if(les_inputs[i].checked)
					nb_check++;
			}
		}
		return nb_check;
	}
	function recuperer_liste_check(div){
		if(typeof(div)=="string")
			div = document.getElementById(div);
		les_inputs = div.getElementsByTagName("input");
		var liste = "";
		for(i=0;i<les_inputs.length;i++){
			if(les_inputs[i].getAttribute("type")=="checkbox"){
				if(les_inputs[i].checked){
					if(liste!="")
						liste += ",";
					liste += les_inputs[i].id.split("_")[2];
				}
			}
		}
		return liste;
	}
	function message(zone,message,type){
		//type : rien ou 0 message, 1 message vert, 2 message rouge
		var div = document.getElementById(zone+"_message");
		if(typeof(type)=="undefined"||type==0){
			div.setAttribute("class","message_sam_info");
			div.innerHTML = message;
		}
		else if(type==1){
			div.setAttribute("class","message_sam_valide");
			div.innerHTML = message;
		}
		else if(type==2){
			div.setAttribute("class","message_sam_erreur");
			div.innerHTML = message;
		}
	}

function supprimer(no,zone){
	var xhr = getXhr();
		xhr.open("POST", "ajax/modif_liste_complete.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("e="+escape(zone)+"&no="+no+"&act=del&no_l=$id_lettre");
	var reponse = eval("("+xhr.responseText+")");
	$("#liste_"+zone).load("aff"+zone+".php", {id:$id_lettre,territoire:$territoire});
}

function modifier_liste(no,zone){
	var xhr = getXhr();
		xhr.open("POST", "ajax/modif_liste_complete.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("e="+escape(zone)+"&no="+no+"&act=del&no_l=<?php echo $id; ?>");
	var reponse = eval("("+xhr.responseText+")");
	$("#liste_"+zone).load("aff"+zone+".php", {id:$id_lettre,territoire:$territoire});
}

function valider_etape(e){
	var xhr = getXhr();
		xhr.open("POST", "ajax/valider_etape.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	if(e!="edito")
		xhr.send("e="+escape(e)+"&no_l=$id_lettre");
	else{
		xhr.send("e="+escape(e)+"&t="+encodeURIComponent(CKEDITOR.instances[e].getData())+"&no_l=$id_lettre");
		CKEDITOR.instances[e].destroy();
	}
	var reponse = eval("("+xhr.responseText+")");
	$("#liste_"+e).load("aff"+e+".php", {id:$id_lettre,territoire:$territoire});
	document.getElementById("etape_"+e).style.backgroundImage = "url('img/etape_"+e+"_ok.png')";
	verif_tte_etape_valide();
	document.location = "#filed_"+e;
}
	function verif_tte_etape_valide(){
		var reg = /_ok.png/;
		if(reg.test(document.getElementById("etape_generalites").style.backgroundImage)){ //Etape 1 validée
			if(reg.test(document.getElementById("etape_edito").style.backgroundImage)){ //Etape 2 validée
				if(reg.test(document.getElementById("etape_agenda").style.backgroundImage)){
					if(reg.test(document.getElementById("etape_repertoire").style.backgroundImage)){
						if(reg.test(document.getElementById("etape_petiteannonce").style.backgroundImage)){
							document.getElementById("div_validation_finale").style.display = "block";
						}
					}
				}
			}
		}
	}
function de_valider_etape(e){
	var xhr = getXhr();
		xhr.open("POST", "ajax/de_valider_etape.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("e="+escape(e)+"&no_l=$id_lettre");
	var reponse = eval("("+xhr.responseText+")");
	$("#liste_"+e).load("aff"+e+".php", {id:$id_lettre,territoire:$territoire});
	document.getElementById("etape_"+e).style.backgroundImage = "url('img/etape_"+e+".png')";
	if(document.getElementById("div_validation_finale").style.display!="none")
		document.getElementById("div_validation_finale").style.display = "none";
	document.location = "#filed_"+e;
}


function modif_objet(input,e){
	if(e.keyCode==13)
		valid_modif_objet(input);
}
function modif_objet_blur(input){
	input.previousSibling.style.display = "inline";
	input.style.display = "none";
}
function valid_modif_objet(input){
	if(input.value.length){
		var xhr = getXhr();
			xhr.open("POST", "ajax/modif_obj_date.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send("o="+escape(input.value)+"&no=$id_lettre");
		var reponse = eval("("+xhr.responseText+")");
		if(reponse[0]){
			input.previousSibling.firstChild.data = input.value;
			input.previousSibling.style.display = "inline";
			input.style.display = "none";
		}
		else{
			alert(reponse[1]);
			modif_objet_blur(input);
		}
	}
	else{
		modif_objet_blur(input);
	}
}
function change_date(select){
	//enregistrement BDD de select.value
	var xhr = getXhr();
		xhr.open("POST", "ajax/modif_obj_date.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("d="+escape(select.value)+"&no=$id_lettre");
	var reponse = eval("("+xhr.responseText+")");
	if(reponse[0]){
		select.previousSibling.firstChild.data = select.options[select.selectedIndex].text;
		select.previousSibling.style.display = "inline";
		select.style.display = "none";
	}
	else{
		alert(reponse[1]);
		annule_date(select);
	}
}
function annule_date(select){
	select.previousSibling.style.display = "inline";
	select.style.display = "none";
}
</script>
AJHE;

include "../inc-header.php";
include "../../../01_include/struct_modal.php";
?>

<?php
//On récupère la semaine courante.
$semaine = ((int) date("W"))+1; // On récupère le numéro de la semaine en cours. La semaine concernée est la semaine qui arrive, d'où le + 1
$annee = date("Y"); // L'année dans laquelle on se situe
$no_jour_aujourdhui = date("w"); //On récupère le numéro du jour d'aujourdhui
$mktime_aujourdhui = time(); //On récupère le timestamp d'aujourd'hui
$nb_jour_ecart = $no_jour_aujourdhui-4; //Le nombre de jours d'écart entre aujourd'hui, et le jeudi (jour n°4) de la semaine en cours
$nb_seconde_un_jour = 24*60*60; //Le nombre de secondes dans une journée
$mktime_jeudi = $mktime_aujourdhui-($nb_jour_ecart*$nb_seconde_un_jour); //On peut ainsi calculer le timestamp du jeudi de la semaine en cours.

$premier_jour_lettre = date("d/m", $mktime_jeudi); //Le jeudi de la semaine en cours et le premier jour de la lettre d'info

$dernier_jour_lettre = date("d/m", ($mktime_jeudi+10*$nb_seconde_un_jour)); //Le dernier jour de la lettre correspond au jeudi de la semaine en cours + 10 jours.

//On créé un tableau contenant les jours de la semaine de lundi à vendredi.
$les_jours_semaine = array();
$les_jours_semaine[0] = array("value"=>$mktime_jeudi-3*$nb_seconde_un_jour,"libelle"=>"lundi ".date("d/m/Y", $mktime_jeudi-3*$nb_seconde_un_jour));
$les_jours_semaine[1] = array("value"=>$mktime_jeudi-2*$nb_seconde_un_jour,"libelle"=>"mardi ".date("d/m/Y", $mktime_jeudi-2*$nb_seconde_un_jour));
$les_jours_semaine[2] = array("value"=>$mktime_jeudi-$nb_seconde_un_jour,"libelle"=>"mercredi ".date("d/m/Y", $mktime_jeudi-$nb_seconde_un_jour));
$les_jours_semaine[3] = array("value"=>$mktime_jeudi,"libelle"=>"jeudi ".date("d/m/Y", $mktime_jeudi));
$les_jours_semaine[4] = array("value"=>$mktime_jeudi+$nb_seconde_un_jour,"libelle"=>"vendredi ".date("d/m/Y", $mktime_jeudi+$nb_seconde_un_jour));
$les_jours_semaine[5] = array("value"=>$mktime_jeudi+2*$nb_seconde_un_jour,"libelle"=>"samedi ".date("d/m/Y", $mktime_jeudi+2*$nb_seconde_un_jour));

if(!$mode_ajout){
	//Si l'on est pas en mode ajout, on regarde les etapes déjà validées
	//étape édito
	$requete_validation_edito = "SELECT etape_valide AS a FROM lettreinfo_edito WHERE no_lettre=:no";
	$res_validation_edito = $connexion->prepare($requete_validation_edito);
	$res_validation_edito->execute(array(":no"=>$id_lettre)) or die("erreur requête ligne 116 : ".$requete_validation_edito);
	$tab_validation_edito = $res_validation_edito->fetchAll();
	if(count($tab_validation_edito)>0)
		$edito_valide = (bool)$tab_validation_edito[0]["a"];
	else
		$edito_valide = false;
	//étape agenda
	$requete_validation_agenda = "SELECT etape_valide AS a FROM lettreinfo_agenda WHERE no_lettre=:no";
	$res_validation_agenda = $connexion->prepare($requete_validation_agenda);
	$res_validation_agenda->execute(array(":no"=>$id_lettre)) or die("erreur requête ligne 116 : ".$requete_validation_agenda);
	$tab_validation_agenda = $res_validation_agenda->fetchAll();
	if(count($tab_validation_agenda)>0)
		$agenda_valide = (bool)$tab_validation_agenda[0]["a"];
	else
		$agenda_valide = false;
	//étape répertoire
	$requete_validation_repertoire = "SELECT etape_valide AS a FROM lettreinfo_repertoire WHERE no_lettre=:no";
	$res_validation_repertoire = $connexion->prepare($requete_validation_repertoire);
	$res_validation_repertoire->execute(array(":no"=>$id_lettre)) or die("erreur requête ligne 116 : ".$requete_validation_repertoire);
	$tab_validation_repertoire = $res_validation_repertoire->fetchAll();
	if(count($tab_validation_repertoire)>0)
		$repertoire_valide = (bool)$tab_validation_repertoire[0]["a"];
	else
		$repertoire_valide = false;
	//étape petites annonces
	$requete_validation_petitesannonces = "SELECT etape_valide AS a FROM lettreinfo_petiteannonce WHERE no_lettre=:no";
	$res_validation_petitesannonces = $connexion->prepare($requete_validation_petitesannonces);
	$res_validation_petitesannonces->execute(array(":no"=>$id_lettre)) or die("erreur requête ligne 116 : ".$requete_validation_petitesannonces);
	$tab_validation_petitesannonces = $res_validation_petitesannonces->fetchAll();
	if(count($tab_validation_petitesannonces)>0)
		$petitesannonces_valide = (bool)$tab_validation_petitesannonces[0]["a"];
	else
		$petitesannonces_valide = false;
}
?>
<style type="text/css">
.message_sam_info{
	padding:5px;
	border-radius: 5px;
	background-color:#E4F5FE;
	background-image:url('img/ico_sam_message.png');
	background-repeat: no-repeat;
	background-position: 3px 3px;
	text-indent:30px;
	color: #385A75;
}
.message_sam_valide{
	padding:5px;
	border-radius: 5px;
	background-color:#F0FEE3;
	background-image:url('img/ico_sam_succes.png');
	background-repeat: no-repeat;
	background-position: 3px 3px;
	text-indent:30px;
	color: #6b7b36;
}
.message_sam_erreur{
	border-radius: 5px;
	padding:5px;
	background-color:#FEE3E3;
	background-image:url('img/ico_sam_erreur.png');
	color: #703131;
	text-indent:26px;
	background-repeat: no-repeat;
	background-position: 3px 3px;
}
#zone_avancement{
	width: 100%;
	position:relative;
	top:50px;
	z-index:100;
	background-color: white;
	height: 60px;
	border-bottom: 1px solid #E3D6C7;
}
.btn_etape_sam{
	float:left;
	<?php if($mode_ajout){ ?>
		opacity:0.6;
	<?php }else{ ?>
		opacity:1;
	<?php } ?>
	width:130px;
	height:25px;
	position:relative;
	top: 5px;
	cursor:pointer;
}
#btn_previsualisation{
	background-image:url('img/fond_bouton.png');
	width: 150px;
	height: 20px;
	cursor: pointer;
	position: absolute;
	bottom: 5px;
	right: 40px;
	font-weight: bold;
	text-align: center;
}
#btn_aide{
	background-image:url('img/fond_bouton.png');
	width: 150px;
	height: 20px;
	cursor: pointer;
	position: absolute;
	bottom: 5px;
	left: 170px;
	font-weight: bold;
	text-align: center;
}
</style>
<div id="zone_avancement">
	<?php if($mode_ajout){ ?>
	<a href="#filed_generalites"><div id="etape_generalites" class="btn_etape_sam" style="background-image:url('img/etape_generalites.png');opacity:1;"></div></a>
	<?php }else{ $nb_valide = 1; ?>
	<a href="#filed_generalites"><div id="etape_generalites" class="btn_etape_sam" style="background-image:url('img/etape_generalites_ok.png');opacity:1;"></div></a>
	<?php }if($edito_valide){ $nb_valide++; ?>
		<a href="#filed_edito"><div id="etape_edito" class="btn_etape_sam" style="background-image:url('img/etape_edito_ok.png');left:-20px;"></div></a>
	<?php }else{ ?>
		<a href="#filed_edito"><div id="etape_edito" class="btn_etape_sam" style="background-image:url('img/etape_edito.png');left:-20px;"></div></a>
	<?php }if($agenda_valide){ $nb_valide++; ?>
		<a href="#filed_agenda"><div id="etape_agenda" class="btn_etape_sam" style="background-image:url('img/etape_agenda_ok.png');left:-40px;"></div></a>
	<?php }else{ ?>
		<a href="#filed_agenda"><div id="etape_agenda" class="btn_etape_sam" style="background-image:url('img/etape_agenda.png');left:-40px;"></div></a>
	<?php }if($repertoire_valide){ $nb_valide++; ?>
		<a href="#filed_repertoire"><div id="etape_repertoire" class="btn_etape_sam" style="background-image:url('img/etape_repertoire_ok.png');left:-60px;"></div></a>
	<?php }else{ ?>
		<a href="#filed_repertoire"><div id="etape_repertoire" class="btn_etape_sam" style="background-image:url('img/etape_repertoire.png');left:-60px;"></div></a>
	<?php }if($petitesannonces_valide){ $nb_valide++; ?>
		<a href="#filed_petiteannonce"><div id="etape_petiteannonce" class="btn_etape_sam" style="background-image:url('img/etape_petiteannonce_ok.png');left:-80px;"></div></a>
	<?php }else{ ?>
		<a href="#filed_petiteannonce"><div id="etape_petiteannonce" class="btn_etape_sam" style="background-image:url('img/etape_petiteannonce.png');left:-80px;"></div></a>
	<?php } ?>
	<?php if($mode_ajout){ ?>
		<div class="actions">
			<button onclick="document.getElementById('adform1').submit();" class="boutonbleu ico-modifier">Ajouter</button>
		</div>
	<?php }else{ ?>
		<a href="envoi.php?id=<?php echo $id_lettre; ?>"><div class="btn_etape_sam" id="div_validation_finale" style="background-image:url('img/etape_validation.gif');left:-50px;<?php if($nb_valide<5) echo "display:none;"; ?>">
			<!--<button type="submit" class="boutonbleu ico-modifier">Modifier</button>-->
			<span style="position:relative;top:4px;left:20px;font-weight:bold;">Valider la lettre</span>
		</div></a>
		<br/>
		<div id="btn_previsualisation"><img src="http://www.ensembleici.fr/img/admin/icoad-voir.png" style="position:relative;top:2px;" />&nbsp;Pr&eacute;visualiser</div>
		<div id="btn_aide"><img src="img/ico_aide.png" style="position:relative;top:2px;" />&nbsp;Aide</div>
	<?php } ?>
</div>

<?php
//Si ajout = 1 : seulement généralités
if($mode_ajout){
?>
<p class="mess"></p>
<form id="adform1" action="domodifajout.php?territoire=<?= $_GET['territoire'] ?>" method="post" class="formA" enctype="multipart/form-data">
<ul>
	<br id="filed_generalites" />
	<br/>
	<br/>
	<br/>
	<fieldset>
		<legend>Généralités</legend>
		<li><label for="objet_lettre">Objet : </label>
			<input type="text" name="objet_lettre" id="objet_lettre" class="input" value="" size="70" /></li>
		<li><label for="date_debut">Date de début : </label>
			<select name="date_debut" id="date_debut">
				<?php
				//On affiche les jours de la semaine en cours
				for($i=0;$i<count($les_jours_semaine);$i++){
				?>
					<option value="<?php echo $les_jours_semaine[$i]["value"]; ?>"<?php if($les_jours_semaine[$i]["value"]==$mktime_jeudi) echo 'selected="selected"'; ?>><?php echo $les_jours_semaine[$i]["libelle"]; ?></option>
				<?php
				}
				?>
			</select></li><br/>
	</fieldset>
</ul></form>
<?php
}
else{
//Sinon, on affiche tout.
	//Si un répertoire est renseigné, la lettre a été validée.
		//On affiche alors tout (visuel + "filtre validé")
		//On affiche aussi un bouton "envoi"
		//On affiche aussi un bouton "prévisualisation"
		//On affiche aussi un bouton "revenir en mode modification"
	//Sinon Si la date d'envoi n'est pas renseignée
		//On affiche toutes les étapes
			//Si l'étape est valide
				//On alors le visuel + "filtre validé" + un bouton "modifier"
			//Sinon
				//Affichage du formulaire
				
				
//On récupère les informations générales sur la lettre courante.
$requete_generale = "SELECT * FROM lettreinfo WHERE no=:no";
$res_requete_generale = $connexion->prepare($requete_generale);
$res_requete_generale->execute(array(":no"=>$id_lettre)) or die("erreur requête ligne 116 : ".$requete_generale);
$tab_requete_generale = $res_requete_generale->fetchAll();
if($tab_requete_generale[0]["date_envoi"]!="0000-00-00")
	$envoye = true;
else
	$envoye = false;
if($tab_requete_generale[0]["repertoire"]!=null&&$tab_requete_generale[0]["repertoire"]!="")
	$valide = true;
else
	$valide = false;
$objet = $tab_requete_generale[0]["objet"];
$date_debut = strtotime($tab_requete_generale[0]["date_debut"]);
?>
<p class="mess"></p>
<form id="adform1" action="domodifajout.php" method="post" class="formA" enctype="multipart/form-data"></form>
<div class="formA">
<ul>
	<div id="filed_generalites"></div>
	<br/><br/><br/>
	<fieldset>
		<legend>Généralités</legend>
		<li><label for="objet_lettre" onclick="this.nextSibling.style.display='none';this.nextSibling.nextSibling.style.display='inline';this.nextSibling.nextSibling.value=this.nextSibling.firstChild.data;this.nextSibling.nextSibling.focus();">Objet : </label><span id="objet_lettre_span" style="position:relative;top:5px;" onclick="this.style.display='none';this.nextSibling.style.display='inline';this.nextSibling.value=this.firstChild.data;this.nextSibling.focus();"><?php echo $objet; ?></span><input type="text" name="objet_lettre" id="objet_lettre" class="input" size="70" style="display:none;" value="<?php echo $objet; ?>" onkeyup="modif_objet(this,event);" onblur="modif_objet_blur(this);" /></li>
		<li><label for="date_debut" onclick="this.nextSibling.style.display='none';this.nextSibling.nextSibling.style.display='inline';this.nextSibling.nextSibling.focus();">Date de début : </label><span name="date_debut_span" style="position:relative;top:5px;" onclick="this.style.display='none';this.nextSibling.style.display='inline';"><?php echo datefr($tab_requete_generale[0]["date_debut"]); ?></span><select name="date_debut" id="date_debut" style="display:none;" onchange="change_date(this);" onblur="annule_date(this)">
				<?php
				//On affiche les jours de la semaine en cours
				for($i=0;$i<count($les_jours_semaine);$i++){
				?>
					<option value="<?php echo $les_jours_semaine[$i]["value"]; ?>"<?php if($les_jours_semaine[$i]["value"]==$mktime_jeudi) echo 'selected="selected"'; ?>><?php echo $les_jours_semaine[$i]["libelle"]; ?></option>
				<?php
				}
				?>
			</select></li><br/>
	</fieldset>
	<div id="filed_edito"></div>
        <br/><br/><br/>
        
	<fieldset>
		<legend>Fichiers PDF</legend>
		<li id="liste_pdf_agenda">
                    <input type="checkbox" id='ckb_gen_pdf_agenda' /> Générer le fichier PDF <b>Agenda</b> et inclure le lien dans la newsletter 
		</li>
                <li id="liste_pdf_annonces">
                    <input type="checkbox" id='ckb_gen_pdf_annonces' /> Générer le fichier PDF <b>Petites Annonces</b> et inclure le lien dans la newsletter 
		</li>
                <button id='generation_pdf_lettreinfo' data-id='<?= $_GET['id'] ?>' class="boutonbleu ico-fleche">Générer les fichiers PDF</button>
                <div id="view_generation_pdf_lettreinfo" style="display:none;">
                    <br/><br/>
                    <div id='view_generation_pdf_agenda' style="display:none;">Voir le fichier PDF <a id='link_pdf_agenda' target='_blank'>agenda</a></div><br/>
                    <div id='view_generation_pdf_annonces' style="display:none;">Voir le fichier PDF <a id='link_pdf_annonces' target='_blank'>petites annonces</a></div><br/>
                    <span id='name_pdf_agenda' style='display:none;'></span>
                    <span id='name_pdf_annonces' style='display:none;'></span>
                    <button id='validate_generation_pdf_lettreinfo' data-id='<?= $_GET['id'] ?>' class="boutonbleu ico-fleche">Valider l'insertion des fichiers PDF</button>
                </div>
                <div id='message_generation_pdf_lettreinfo' style='display:none;'>
                    Les fichiers PDF seront ajoutés à la lettre d'infos
                </div>
	</fieldset>
        
	<div id="filed_pdf"></div>
        
	<br/><br/><br/>
	<fieldset>
		<legend>&Eacute;dito</legend>
		<li id="liste_edito">
		<!--<table><tr><td><textarea name="edito" id="edito"></textarea></td>
		<td>
			<button class="boutonbleu ico-ajout">Ajouter une structure</button><br/>
			<button class="boutonbleu ico-ajout">Ajouter un événement</button><br/>
			<button class="boutonbleu ico-ajout">Ajouter une petite annonce</button><br/>
		</td></tr></table>
		<button class="boutonbleu ico-fleche">Valider l'étape</button>-->
		</li>
	</fieldset>
	<div id="filed_agenda"></div>
	<br/><br/><br/>
	<fieldset>
		<legend>Agenda</legend>
		<li id="liste_agenda">
			<!--<table>
				<tr>
					<td colspan="2"><label>&Eacute;vénements :</label></td>
				</tr>
				<tr>
					<td style="width:500px;" id="liste_evenement">
					</td>
					<td>
						<button class="boutonbleu ico-ajout">Ajouter un événement</button>
					</td>
				</tr>
				<tr>
					<td colspan="2"><label>Atelier / Cours / Stage :</label></td>
				</tr>
				<tr>
					<td style="width:500px;" id="liste_evenementrecurrent">
					</td>
					<td>
						<button class="boutonbleu ico-ajout">Ajouter un atelier / cours / stage</button>
					</td>
				</tr>
			</table>
			<button class="boutonbleu ico-fleche">Valider l'étape</button>-->
		</li>
	</fieldset>
	<div id="filed_repertoire"></div>
	<br/><br/><br/>
	<fieldset>
		<legend>Répertoire</legend>
		<li id="liste_repertoire">
			<!--<table>
				<tr>
					<td style="width:500px;" id="liste_repertoire">
					</td>
					<td>
						<a id="ajoutrepertoire" class="boutonbleu ico-ajout" title="Promouvoir une structure" href="">Promouvoir une structure</a>
					</td>
				</tr>
			</table>-->
		</li>
	</fieldset>
	<div id="filed_petiteannonce"></div>
	<br/><br/><br/>
	<fieldset>
		<legend>Petites annonces</legend>
		<li id="liste_petiteannonce">
			<!--<table>
				<tr>
					<td style="width:500px;">
					</td>
					<td>
						<button class="boutonbleu ico-ajout">Ajouter une petite annonce</button>
					</td>
				</tr>
			</table>
			<button class="boutonbleu ico-fleche">Valider l'étape</button>-->
		</li>
	</fieldset>
	<br/><br/>
	<fieldset>
		<legend>Publicités</legend>
		<li id="zone_publicite"></li>
	</fieldset>
	<fieldset>
		<legend>Nos partenaires</legend>
                <button id='add_partenaire' data-id='<?= $territoire ?>' class="boutonbleu ico-fleche">Ajouter un partenaire</button>
		<li id="zone_partenaire_institutionnel"></li>
	</fieldset>
</ul>

</div>
<?php
}
?>

<?php
include "../inc-footer.php";
}
else {
  $_SESSION['message'] .= "Erreur : veuillez sélectionner ".$cc_une." à modifier.";  
  header("location:".$URLadmin."admin.php");
  exit();
}
 
?>
