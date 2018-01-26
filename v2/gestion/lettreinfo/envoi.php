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

if($id_lettre){

	$titrepage = "Envoi d'$cc_une";

     
  // Lignes à ajouter au header
	$cc_cettemin_slash = addslashes($cc_cettemin);
    $ajout_header = <<<AJHE
<style type="text/css">
.formA label {
    display: inline-block;
    float: none;
    font-weight: bold;
    margin: 0;
    padding: 5px 5px 0 0;
    text-align: right;
	width: auto;
}
</style>
<script type="text/javascript" src="_f.js"></script>
<script type="text/javascript" src="../../js/fonction_auto_presentation.js"></script>
<script type="text/javascript" src="./message/message.js"></script>
<script type="text/javascript">
	var Y_MENU_DEPART;
	var ANCRE_COURANTE;
	var NO_LETTRE = $id_lettre;
	function getXhr(){if(window.XMLHttpRequest) xhr=new XMLHttpRequest();else if(window.ActiveXObject){try{xhr=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){xhr=new ActiveXObject("Microsoft.XMLHTTP");}}else{alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest, veuillez le mettre Ã  jour");xhr=false;}return xhr;}
	function ecran(){if (document.body){var larg=(document.body.clientWidth);var haut=(document.body.clientHeight);}else{var larg=(window.innerWidth);var haut=(window.innerHeight);}return {"x":larg,"y":haut};}
	function vide(e){if(e.firstChild){e.removeChild(e.firstChild);vide(e);}}
  // window.onload = function()
  // {
  // };
	
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
	
	function ouvrir_field(btn,id,e){
		if(id=="field_test"){
			if(confirm("Cette action génère le fichier sur le site !")){
				document.getElementById("btn_revenir_creation").style.display = "none";
				document.getElementById("btn_previsualisation").style.display = "none";
				var old_field = document.getElementById("field_recapitulatif");
				validation();
				test = true;
			}
			else{
				test = false;
			}
		}
		else if(id=="field_liste"){
			document.getElementById("field_test").style.display = "none";
			var old_field = document.getElementById("field_test");
			test = true;
		}
		else if(id=="field_suivi"){
			test = true;
			document.getElementById("adresse_suivi").value = document.getElementById("adresse_test").value;
			document.getElementById("zone_barre_liste").style.display = "inline-block";
			var old_field = document.getElementById("field_liste");
			creer_liste();
		}
		else if(id=="field_envoi"){
			//On test les champs, et créait le récapitulatif afin de demander confirmation
			if(document.getElementById("adresse_suivi").value!=""){
				var old_field = document.getElementById("field_suivi");
				if(test_email_valide(document.getElementById("adresse_suivi").value)){
					var add = escape(document.getElementById("adresse_suivi").value);
					//On regarde les radios et la case à cocher.
					if(document.getElementById("mail_true").checked){
						var m_s = 1;
						var s_s = document.getElementById("select_min").value;
						//Il y a un suivie, on regarde le temps.
						var recapitulatif = "Un mail de suivi vous sera envoyé à l'adresse '"+document.getElementById("adresse_suivi").value+"' toutes les "+document.getElementById("select_min").options[document.getElementById("select_min").selectedIndex].text+" minutes";
					}
					else{
						var m_s = 0;
						var s_s = 0;
						//Il n'y a pas de suivi
						var recapitulatif = "";
					}
					if(document.getElementById("envoi_fin").checked){
						var m_f = 1;
						//On envoi un mail à la fin
						if(recapitulatif=="")
							recapitulatif = "Un mail vous sera envoyé à l'adresse '"+document.getElementById("adresse_suivi").value+"' seulement à la fin de l'envoi";
						else
							recapitulatif += ", ainsi qu'à la fin de l'envoi.";
					}
					else{
						var m_f = 0;
						//On envoi un mail à la fin
						if(recapitulatif=="")
							test = false;
						else
							recapitulatif += ".";
					}
					if(recapitulatif!=""){
						if(confirm(recapitulatif))
							test = true;
						else
							test = false;
					}
					else{
						if(confirm("Vou avez saisie une adresse de suivie, mais aucun mail ne lui sera envoyé ..."))
							test = true;
						else
							test = false;
					}
				}
				else{
					message("Vous devez saisir une adresse valide !",{'largeur':150, 'seconde' : 1.5 },e);
					test = false;
				}
			}
			else{
				//Si aucune adresse n'est saisie, il faut que la case soit décoché, et que la deuxième radio soit cochée.
				if(!document.getElementById("envoi_fin").checked&&document.getElementById("mail_false").checked){
					var recapitulatif = "Aucun mail ne vous sera envoyé";
					if(confirm(recapitulatif)){
						test = true;
						var add = "aucune"
						var m_s = 0;
						var m_f = 0;
						var s_s = 0;
					}
					else
						test = false;
				}
				else{
					test = false;
					message("Vous devez saisir une adresse de suivie !",{'largeur':170, 'seconde' : 1.5 },e);
				}
			}
			if(test){
				//On rend non modifiable tous les champs
				document.getElementById("adresse_suivi").setAttribute("disabled","disabled");
				document.getElementById("mail_true").setAttribute("disabled","disabled");
				document.getElementById("mail_false").setAttribute("disabled","disabled");
				document.getElementById("envoi_fin").setAttribute("disabled","disabled");
				document.getElementById("select_min").setAttribute("disabled","disabled");
				//On enregistre dans la base de données.
				maj_option_suivi(add,m_s,m_f,s_s);
			}
		}
		else
			test = true;
		if(test){
			var field = document.getElementById(id);
				field.style.display = "block";
				field.style.backgroundColor = "#e5eef2";
			old_field.style.backgroundColor = "#f0edea";
			btn.parentNode.removeChild(btn);
		}
	}
	
	function validation(){
		var xhr = getXhr();
			xhr.open("POST", "ajax/valider_lettre.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send("no="+NO_LETTRE);
		var reponse = eval("("+xhr.responseText+")");
		if(reponse[0]){
			NO_ENVOI = reponse[1];
			REPERTOIRE = reponse[2];
			var t = document.getElementById("table_recap");
			var tr = document.createElement("tr");
			var td = document.createElement("td");
				td.setAttribute("colspan","2");
			var label = document.createElement("label");
				label.appendChild(document.createTextNode("répertoire : "));
			var a = document.createElement("a");
				a.setAttribute("href",REPERTOIRE+"index.html");
				a.setAttribute("target","_blank");
				a.appendChild(document.createTextNode(REPERTOIRE));
			td.appendChild(label);
			td.appendChild(a);
			tr.appendChild(td);
			t.appendChild(tr);
		}
		else{
			alert(reponse[1]);
		}
	}
	function annuler_validation(){
		var xhr = getXhr();
			xhr.open("POST", "ajax/valider_lettre.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send("no="+NO_LETTRE+"&cancel=1");
		var reponse = eval("("+xhr.responseText+")");
		if(reponse[0]){
			document.location = "modifajout.php?id="+NO_LETTRE;
		}
		else{
			alert(reponse[1]);
		}
	}
	function maj_option_suivi(a,ms,mf,ss){
		var xhr = getXhr();
			xhr.open("POST", "ajax/option_suivi.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send("no="+NO_LETTRE+"&a="+a+"&m_s="+ms+"&m_f="+mf+"&s_s="+ss);
		var reponse = eval("("+xhr.responseText+")");
		if(reponse[0]){
			message(reponse[1],1);
		}
		else{
			alert(reponse[1]);
		}
	}
	
	function envoi_test(e){
		//On vérifie l'adresse mail
		if(test_email_valide(document.getElementById("adresse_test").value)){
			//On met à jour l'interface
			document.getElementById("btn_envoyer_test").style.display = "none";
			document.getElementById("btn_passer_test").style.display = "none";
			document.getElementById("ou_test").style.display = "none";
			document.getElementById("etat_test_3").style.display = "none";
			document.getElementById("etat_test_1").style.display = "inline";
			envoyer_test(e,escape(document.getElementById("adresse_test").value));
		}
		else{
			message("adresse mail invalide !",1,e);
		}
	}
	function passer_test(btn,e){
		if(confirm("Vous n'allez pas testé l'envoi !"))
			ouvrir_field(btn,'field_liste',e);
	}
		function envoyer_test(e,a){
			message("envoi d'un test",0.5,e);
			var xhr = getXhr();
				xhr.open("POST", "ajax/envoi_mail_test.php", false);
				xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xhr.send("no="+NO_LETTRE+"&a="+a);
			var reponse = eval("("+xhr.responseText+")");
			if(reponse){
				document.getElementById("etat_test_1").style.display = "none";
				document.getElementById("etat_test_2").style.display = "inline";
			}
		}
		function revendiquer_test(){
			document.getElementById("etat_test_2").style.display = "none";
			document.getElementById("etat_test_3").style.display = "inline";
		}
	
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
	
	function creer_liste(e){
		CREATION_LISTE_EN_COURS = true;
		PAS_AVANCEMENT = 1;
		OLD_P = 0;
		AVANCEMENT_MOYEN = 0;
		OLD_NB_MS = 0;
		OLD_NB_PX = 0;
		//1. On vide l'éventuelle liste
		document.getElementById("estimation_temps_liste").firstChild.data = "initialisation de la liste";
		vider_liste();
		message("création de la liste d'envoi ("+NB_MAIL_TOTAL_LISTE+" mails à préparer)",1.5,e);
		setTimeout("interroge_liste()",500);
		//2. On lance la création de la liste
		creer_liste_rec(NO_LETTRE);
	}
			function creer_liste_rec(no,no_liste){
				if(typeof(no_liste)=="undefined")
					no_liste = 1;
				//On appelle le fichier liste.php
				var xhr = getXhr();
					xhr.onreadystatechange = function(){
						if(xhr.readyState == 4){
							if(xhr.status == 200){
								var reponse = eval("("+xhr.responseText+")");
								if(reponse[0]){ //Tout se passe bien
									if(reponse[1]){ //Il reste encore des adresses à traiter
										creer_liste_rec(no,reponse[2]);
									}
									else{ //C'est fini
										if(document.getElementById("envoi_auto").checked)
											debuter_envoi(document.getElementById("bout_d_envoi"));
									}
								}
								else{
									alert("une erreur s'est produite, veuillez recharger la page !");
								}
							}
						}
					};
					xhr.open("POST", "ajax/liste_test.php", true);
					xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
					xhr.send("no="+no+"&no_liste="+no_liste);
			}
		function interroge_liste(){
			//On récupère l'avancement de la liste
			var nb_cour = nb_mail_liste();
			//On calcul le pourcentage
			var p = Math.floor(nb_cour*100/NB_MAIL_TOTAL_LISTE);
			
			//On estime le temps qu'il reste
			if(AVANCEMENT_MOYEN==0)
				AVANCEMENT_MOYEN = p;
			else{
				moyenne_temp = p-OLD_P;
				AVANCEMENT_MOYEN = (AVANCEMENT_MOYEN+moyenne_temp)/2;
				var nb_ms_100 = 100*500/AVANCEMENT_MOYEN;
				var nb_ms = nb_ms_100*(100-p)/100;
				if(OLD_NB_MS!=0&&nb_ms<OLD_NB_MS){
					OLD_NB_MS = nb_ms;
					if(nb_ms!=0)
						document.getElementById("estimation_temps_liste").firstChild.data = Math.ceil(nb_ms/1000)+" secondes restantes.";
					else
						document.getElementById("estimation_temps_liste").firstChild.data = "terminé";
				}
				else{
					if(OLD_NB_MS==0)
						OLD_NB_MS = nb_ms;
				}
			}
			OLD_P = p;
			
			avancement_barre("zone_barre_liste",p);
			if(p<100&&CREATION_LISTE_EN_COURS)
				setTimeout("interroge_liste()",500);
		}
	function debuter_envoi(btn,e){
		if(!CREATION_LISTE_EN_COURS&&!ENVOI_EN_COURS){
			ENVOI_EN_COURS = true;
			if(REINITIALISE_VARIABLE_ENVOI){
				PAS_AVANCEMENT = 1;
				OLD_P = 0;
				AVANCEMENT_MOYEN = 0;
				OLD_NB_MS = 0;
				OLD_NB_PX = 0;
			}
			else{
				document.getElementById("avancement_envoi").style.backgroundImage = document.getElementById("avancement_envoi").style.backgroundImage.replace(".png",".gif");
			}
			btn.value = "pause";
			btn.setAttribute("onclick","stoper_envoi(this,event)");
			if(NB_MAIL_ENVOYE==0){
				if(typeof(e)!="undefined")
					message("début de l'envoi (<b>"+(NB_MAIL_TOTAL_LISTE-NB_MAIL_ENVOYE)+"</b> mails à envoyer)",2,e);
				else
					message("début de l'envoi (<b>"+(NB_MAIL_TOTAL_LISTE-NB_MAIL_ENVOYE)+"</b> mails à envoyer)",2);
			}
			else{
				if(typeof(e)!="undefined")
					message("reprise de l'envoi (<b>"+(NB_MAIL_TOTAL_LISTE-NB_MAIL_ENVOYE)+"</b> mails à envoyer)",1,e);
				else
					message("reprise de l'envoi (<b>"+(NB_MAIL_TOTAL_LISTE-NB_MAIL_ENVOYE)+"</b> mails à envoyer)",1);
			}
			setTimeout("interroge_envoi()",1000);
			//Si un suivi est demandé (au niveau des secondes)
			if(document.getElementById("mail_true").checked){
				var nb_ms = document.getElementById("select_min").value*1000;
				setTimeout("suivi_rec('"+escape(document.getElementById("adresse_suivi").value)+"',"+nb_ms+")",nb_ms);
			}
			if(document.getElementById("envoi_fin").checked)
				a = escape(document.getElementById("adresse_suivi").value);
			else
				a = false;
			envoyer_rec(a);
		}
		else{
			message("la liste n'est pas prête !",2,e);
		}
	}
		function suivi_rec(a,ms){
			if(ENVOI_EN_COURS){
				//On envoi un mail contenant les infos principales
				var xhr = getXhr();
					xhr.open("POST", "ajax/envoi_mail_suivi.php", false);
					xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
					xhr.send("no="+NO_LETTRE+"&a="+a+"&f=0");
				setTimeout("suivi_rec('"+a+"',"+ms+")",ms);
			}
		}
		function envoi_mail_fin(a){
			var xhr = getXhr();
				xhr.open("POST", "ajax/envoi_mail_suivi.php", false);
				xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xhr.send("no="+NO_LETTRE+"&a="+a+"&f=1");
		}
		var XHR_ENVOI = false;
		function envoyer_rec(a){
			if(ENVOI_EN_COURS){
				if(typeof(no_liste)=="undefined")
					no_liste = 1;
				//On appelle le fichier liste.php
				var XHR_ENVOI = getXhr();
					XHR_ENVOI.onreadystatechange = function(){
						if(XHR_ENVOI.readyState == 4){
							if(XHR_ENVOI.status == 200){
								var reponse = eval("("+XHR_ENVOI.responseText+")");
								if(reponse[0]){ //Tout se passe bien
									if(reponse[1]){ //Il reste encore des adresses à traiter
										XHR_ENVOI = false;
										envoyer_rec(a);
									}
									else{
										XHR_ENVOI = false;
										if(a!=false)
											envoi_mail_fin(a);
										document.getElementById("avancement_envoi").style.backgroundImage = "url('img/fond_barre_chargement_termine.png')";
										document.getElementById("avancement_envoi").style.width = "100%";
										document.getElementById("avancement_envoi").parentNode.nextSibling.firstChild.data = "100 %";
										alert(reponse[2]);
										document.location = "admin.php";
									}
								}
								else{
									alert("une erreur s'est produite, veuillez recharger la page !");
								}
							}
						}
					};
					XHR_ENVOI.open("POST", "ajax/envoyer.php", true);
					XHR_ENVOI.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
					XHR_ENVOI.send("no="+NO_LETTRE+"&nb="+NB_MAIL_TOTAL_LISTE);
			}
		}
	function stoper_envoi(btn,e){
		if(ENVOI_EN_COURS){
			if(XHR_ENVOI!=false){
				XHR_ENVOI.abort();
				XHR_ENVOI = false;
			}
			message("envoi en pause",1,e);
			document.getElementById("avancement_envoi").style.backgroundImage = document.getElementById("avancement_envoi").style.backgroundImage.replace(".gif",".png");
			btn.value = "reprendre";
			btn.setAttribute("onclick","debuter_envoi(this,event)");
			REINITIALISE_VARIABLE_ENVOI = false;
			ENVOI_EN_COURS = false;
		}
	}
		function interroge_envoi(){
			//On récupère l'avancement de la liste
			var nb_cour = nb_mail_envoye();
			//On calcul le pourcentage
			var p = Math.floor(nb_cour*100/NB_MAIL_TOTAL_LISTE);
			
			//On estime le temps qu'il reste
			if(AVANCEMENT_MOYEN==0)
				AVANCEMENT_MOYEN = p;
			else{
				moyenne_temp = Math.floor(p-OLD_P);
				AVANCEMENT_MOYEN = (AVANCEMENT_MOYEN+moyenne_temp)/2;
				var nb_ms_100 = 100*1000/AVANCEMENT_MOYEN;
				var nb_ms = nb_ms_100*(100-p)/100;
				if(OLD_NB_MS!=0&&nb_ms<OLD_NB_MS){
					OLD_NB_MS = nb_ms;
					if(nb_ms!=0)
						document.getElementById("estimation_temps_envoi").firstChild.data = Math.ceil(nb_ms/1000)+" secondes restantes.";
					else
						document.getElementById("estimation_temps_envoi").firstChild.data = "terminé";
				}
				else{
					if(OLD_NB_MS==0)
						OLD_NB_MS = nb_ms;
				}
			}
			OLD_P = p;
			
			avancement_barre("zone_barre_envoi",p);
			if(p<100&&ENVOI_EN_COURS)
				setTimeout("interroge_envoi()",1000);
		}
	function avancement_barre(b,p){
		if(typeof(b)=="string")
			b = document.getElementById(b);
		var barre = b.firstChild;
		var avancement = barre.firstChild;
		var avancement_p = barre.nextSibling;
		avancement_p.firstChild.data = p+" %";
		if(p<100){
			avancement_progressif(avancement.id,p,true);
		}
		else{
			if(typeof(AVANCEMENT_PROGRESSIF[avancement.id])!="undefined"&&AVANCEMENT_PROGRESSIF[avancement.id]!=false){
				clearTimeout(AVANCEMENT_PROGRESSIF[avancement.id]);
				AVANCEMENT_PROGRESSIF[avancement.id] = false;
			}
			avancement.style.backgroundImage = "url('img/fond_barre_chargement_termine.png')";
			avancement.style.width = p+"%";
			if(CREATION_LISTE_EN_COURS)
				CREATION_LISTE_EN_COURS = false;
			else if(ENVOI_EN_COURS){
				ENVOI_EN_COURS = false;
			}
		}
	}
	function avancement_progressif(id,p,premier){
		var div = document.getElementById(id);
		var conteneur = div.parentNode;
		var nb_px = Math.floor(conteneur.offsetWidth*p/100);
		if(div.offsetWidth<nb_px-PAS_AVANCEMENT){
			if(premier&&typeof(AVANCEMENT_PROGRESSIF[id])!="undefined"&&AVANCEMENT_PROGRESSIF[id]!=false){
				clearTimeout(AVANCEMENT_PROGRESSIF[id]);
				AVANCEMENT_PROGRESSIF[id] = false;
				if(OLD_NB_PX!=0){
					var dif = OLD_NB_PX-div.offsetWidth;
					if(dif>PAS_AVANCEMENT)
						PAS_AVANCEMENT++;
					else if(dif==0)
						PAS_AVANCEMENT--;
				}
				OLD_NB_PX = nb_px;
			}
			div.style.width = div.offsetWidth+PAS_AVANCEMENT+"px";
			AVANCEMENT_PROGRESSIF[id] = setTimeout("avancement_progressif('"+id+"',"+p+",false)",50);
		}
		else{
			div.style.width = nb_px+"px";
			AVANCEMENT_PROGRESSIF[id] = false;
		}
	}
	function nb_mail_liste(){
		var xhr = getXhr();
			xhr.open("POST", "ajax/nb_liste.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send(null);
		NB_MAIL_LISTE = xhr.responseText;
		return NB_MAIL_LISTE;
	}
	function vider_liste(){
		var xhr = getXhr();
			xhr.open("POST", "ajax/vider_liste.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send(null);
		document.getElementById("estimation_temps_liste").firstChild.data = "création de la liste";
	}
	function nb_mail_envoye(){
		var xhr = getXhr();
			xhr.open("POST", "ajax/nb_envoi.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send("nb="+NB_MAIL_TOTAL_LISTE);
		if(xhr.responseText!="false")
			NB_MAIL_ENVOYE = xhr.responseText;
		return NB_MAIL_ENVOYE;
	}
	var ENVOI_EN_COURS = false;
	var ENVOI_AUTO = false;
	var CREATION_LISTE_EN_COURS = false;
	var AVANCEMENT_PROGRESSIF = new Array();
	
	var OLD_P = 0;
	var AVANCEMENT_MOYEN = 0;
	var OLD_NB_PX = 0;
	var PAS_AVANCEMENT = 1;
	var OLD_NB_MS = 0;
	var REINITIALISE_VARIABLE_ENVOI = true;
</script>
AJHE;

include "../inc-header.php";

/*********
On récupère les 3 informations dont on a besoin pour la lettre:
	-objet
	-date
	-répertoire
	**/
$requete_lettre = "SELECT date_debut AS d,objet AS o,repertoire AS r, no_envoi AS n_e, territoires_id FROM lettreinfo WHERE no=:no";
$res_lettre = $connexion->prepare($requete_lettre);
$res_lettre->execute(array(":no"=>$id_lettre)) or die("erreur requête ligne 116 : ".$requete_lettre);
$tab_lettre = $res_lettre->fetchAll();
$objet = $tab_lettre[0]["o"];
$date = $tab_lettre[0]["d"];
$territoire = $tab_lettre[0]["territoires_id"];
// $d = date_add($date, date_interval_create_from_date_string('10 days'));
$dates = "du ".datefr($date)." au ".date("d/m/Y",(strtotime($date)+3600*24*10));
$n_e = $tab_lettre[0]["n_e"];
$repertoire = $tab_lettre[0]["r"];
if($n_e!=0){
        $valide = true;
}
else{
        $valide = false;
        if($repertoire==null)
                $repertoire="";
}

$liste_creee = false;
$envoi_commence = false;
$suivi = false;
if($valide){
        //On récupère dans lettreinfo_envoi les informations d'envoi et de suivi.
        $requete_envoi = "SELECT date_debut AS d_d,date_fin AS d_f, mail_suivi, mail_fin, adresse_suivi, seconde_suivi, nb_liste, nb_envoi FROM lettreinfo_envoi WHERE no=:no";
        $res_envoi = $connexion->prepare($requete_envoi);
        $res_envoi->execute(array(":no"=>$n_e)) or die("erreur requête ligne 116 : ".$requete_envoi);
        $tab_envoi = $res_envoi->fetchAll();
        $mail_suivi = $tab_envoi[0]["mail_suivi"];
        $mail_fin = $tab_envoi[0]["mail_fin"];
        $adresse_suivi = $tab_envoi[0]["adresse_suivi"];
        $seconde_suivi = $tab_envoi[0]["seconde_suivi"];
        $nb_liste = $tab_envoi[0]["nb_liste"];
        $nb_envoi = $tab_envoi[0]["nb_envoi"];
        $date_debut = $tab_envoi[0]["d_d"];
        $date_fin = $tab_envoi[0]["d_f"];
        if($nb_liste!=0){
                $liste_creee = true;
                if($adresse_suivi!=null&&$adresse_suivi!=""){
                        $suivi = true;
                        if($date_debut!=null){
                                $envoi_commence = true;
                        }
                }
        }
}
else{
        $nb_liste = 0;
        $nb_envoi = 0;
}
if($nb_liste==0){
        //On récupère le nombre d'inscrit dans la table newsletter
//        $requete_inscrit = "SELECT COUNT(no) AS nb FROM newsletter WHERE etat=1";
        $requete_inscrit = "SELECT COUNT(N.no) AS nb FROM newsletter N, communautecommune_ville V, communautecommune C WHERE N.etat=1 AND
            V.no_ville = N.no_ville AND V.no_communautecommune = C.no AND C.territoires_id = :t";
        $res_inscrit = $connexion->prepare($requete_inscrit);
        $res_inscrit->execute(array(":t" => $territoire)) or die("erreur requête ligne 116 : ".$requete_inscrit);
        $tab_inscrit = $res_inscrit->fetchAll();
        $nb_inscrit = $tab_inscrit[0]["nb"];

        //On récupère le nombre de membre abonné à la newsletter
//		$requete_abonne = "SELECT COUNT(no) AS nb FROM utilisateur WHERE newsletter=1";
        $requete_abonne = "SELECT COUNT(U.no) AS nb FROM utilisateur U, communautecommune_ville V, communautecommune C WHERE U.newsletter=1 AND 
            V.no_ville = U.no_ville AND V.no_communautecommune = C.no AND C.territoires_id = :t";
        $res_abonne = $connexion->prepare($requete_abonne);
        $res_abonne->execute(array(":t" => $territoire)) or die("erreur requête ligne 116 : ".$requete_abonne);
        $tab_abonne = $res_abonne->fetchAll();
        $nb_abonne = $tab_abonne[0]["nb"];
        
        // ********************** A ENLEVER PLUS TARD *******************
        // en attendant la refonte du back-office, 
        if ($territoire == 1) {
            $requete_abonne2 = "SELECT COUNT( U.no ) AS nb FROM utilisateur U WHERE U.newsletter = 1 AND U.no NOT IN (SELECT U2.no FROM utilisateur U2, 
                communautecommune_ville V, communautecommune C WHERE U2.newsletter = 1 AND V.no_ville = U2.no_ville AND V.no_communautecommune = C.no AND 
                C.territoires_id = 2)"; 
            $res_abonne2 = $connexion->prepare($requete_abonne2);
            $res_abonne2->execute(array(":t" => $territoire)) or die("erreur requête ligne 116 : ".$requete_abonne2);
            $tab_abonne2 = $res_abonne2->fetchAll();
            $nb_abonne2 = $tab_abonne2[0]["nb"];
            $nb_total = $nb_inscrit+$nb_abonne + $nb_abonne2;
        }
        else {
            $nb_total = $nb_inscrit+$nb_abonne;
        }
        
        //*****************************************************************

        
//        $nb_total = $nb_inscrit+$nb_abonne;
}
else{
        $nb_total = $nb_liste;
}
?>
<script type="text/javascript">
var NB_MAIL_TOTAL_LISTE = <?php echo $nb_total; ?>;
var NB_MAIL_ENVOYE = <?php echo $nb_envoi; ?>;
var NB_MAIL_LISTE = <?php echo $nb_liste; ?>;
</script>
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
</style>
<p class="mess"></p>
<div class="formA">
<ul>
	<br/>
	<fieldset <?php if(!$valide) echo 'style="background-color:#e5eef2;"'; ?> id="field_recapitulatif"><legend>R&eacute;capitulatif</legend><li id="zone_recap">
		<?php if(!$valide){ ?>
			<table style="width:100%;" id="table_recap">
			<tr>
			<td><label for="objet">Objet&nbsp;:&nbsp;</label><span><?php echo $objet; ?></span></td>
			<td rowspan="2"><input type="button" value="prévisualiser" onclick="ouvrir_lettre();" id="btn_previsualisation" /></td>
			</tr>
			<tr>
			<td><label for="date">Dates&nbsp;:&nbsp;</label><span><?php echo $dates; ?></span></td>
			</tr>
			</table>
			<br/>
			<br/>
			<input type="button" value="continuer" onclick="ouvrir_field(this,'field_test',event);" />
			<input type="button" value="revenir en mode création" onclick="document.location='modifajout.php?id=<?php echo $id_lettre; ?>';" id="btn_revenir_creation" />
		<?php
		}
		else{
		?>
			<table style="width:100%;" id="table_recap">
			<tr>
			<td><label for="objet">Objet&nbsp;:&nbsp;</label><span><?php echo $objet; ?></span></td>
			</tr>
			<tr>
			<td><label for="date">Dates&nbsp;:&nbsp;</label><span><?php echo $dates; ?></span></td>
			</tr>
			<tr>
			<td><label>Répertoire&nbsp;:&nbsp;</label><a href="<?php echo $repertoire; ?>index.html" target="_blank"><?php echo $repertoire; ?></a></td>
			</tr>
			</table>
		<?php
		}
		?>
	</li></fieldset>
	
	<br/>
	
	<?php
	if(!$liste_creee){
	?>
	<fieldset id="field_test" style="background-color:#e5eef2;<?php if(!$valide) echo "display:none;"; ?>" style=""><legend>Tester l'envoi</legend>
		
		<table>
			<tr><td><input type="text" value="<?php echo $_SESSION["UserConnecte_email"]; ?>" id="adresse_test" /></td><td><input type="button" value="envoyer un test" onclick="envoi_test(event);" id="btn_envoyer_test" /><span id="etat_test_1" style="display:none;">envoi en cours</span><span id="etat_test_2" style="display:none;">envoi terminé, vous convient-il? <input type="button" value="oui" onclick="ouvrir_field(this,'field_liste',event);" />&nbsp;<input type="button" value="non" onclick="revendiquer_test();" /></span><span id="etat_test_3" style="display:none;"><input type="button" value="revenir en mode création" onclick="annuler_validation();" />&nbsp;<input type="button" value="renvoyer un test" onclick="envoi_test(event)" /></span></td></tr>
			<tr><td style="text-align:right;"><span id="ou_test">ou&nbsp;</span></td><td><input type="button" value="se passer du test" onclick="passer_test(this,event);" id="btn_passer_test" /></td></tr>
		</table>

	</fieldset>
	<?php
	}
	?>
	
	<br/>
	
	<fieldset id="field_liste" style="<?php if(!$liste_creee) echo "display:none;"; ?>"><legend>Liste de diffusion</legend><li id="zone_liste">
	
		<?php
		//On récupère le nombre de membres inscrits
		if(!$liste_creee){
			echo "<b>".$nb_abonne."</b>&nbsp;inscrits &agrave; la newsletter.";
			echo "<br/>";
			echo "<b>".$nb_inscrit."</b>&nbsp;adresses dans la liste de diffusion hors membre.";
			echo "<br/>";
			echo "<b>".($nb_abonne+$nb_inscrit)."&nbsp;mails seront envoy&eacute;s.</b>";
			echo "<br/>";
			echo "<br/>";
			echo "<input type=\"button\" value=\"continuer\" onclick=\"ouvrir_field(this,'field_suivi',event);\" />";
		?>
			<div style="display:none;" id="zone_barre_liste"><div style="width:600px;height:10px;background-image:url('img/fond_barre_chargement.png');display:inline-block;" id="barre_liste"><div id="avancement_liste" style="width:1px;height:100%;background-image:url('img/fond_barre_avancement.gif');"></div></div><div style="display:inline-block;" id="avancement_liste_p">0 %</div><div id="estimation_temps_liste" style="width:100%;text-align:center;">&nbsp;</div></div>
		<?php
		}
		else{
			echo "La liste est déjà créée.<br/>";
			echo "<b>".$nb_liste."</b>&nbsp;mails à envoyer.";
		}
		?>
	</li></fieldset>
	<br/>
	<fieldset id="field_suivi" style="<?php if(!$liste_creee) echo "display:none;"; else if($liste_creee&&!$suivi) echo 'background-color:#e5eef2;'; ?>"><legend>Suivi de l'envoi</legend><li id="zone_suivi">
		<?php
		if(!$suivi){
		?>
			<label for="adresse_suivi">Adresse de suivi&nbsp;:&nbsp;</label><input type="text" id="adresse_suivi" <?php if($liste_creee){ echo 'value="'.$_SESSION["UserConnecte_email"].'"'; } ?> />
			<br/>
			<input type="radio" id="mail_true" name="mail" checked="checked" /><label for="mail_true">&nbsp;m'envoyer un mail de suivi toutes les&nbsp;</label><select id="select_min" style="display:inline;"><option value="120">2</option><option value="300">5</option><option value="600" selected="selected">10</option><option value="1800">30</option><option value="3600">60</option></select><b>&nbsp;minutes.</b>
			<br/>
			<input type="radio" id="mail_false" name="mail" /><label for="mail_false">&nbsp;ne pas m'envoyer de mail de suivi.</label>
			<br/>
			<br/>
			<input type="checkbox" checked="checked" id="envoi_fin" /><label for="envoi_fin">&nbsp;m'envoyer un mail lorsque l'envoi s'est termin&eacute;.</label>
			<br/>
			<br/>
			<input type="checkbox" id="envoi_auto" /><label for="envoi_auto">&nbsp;D&eacute;buter automatiquement l'envoi apr&egrave;s la g&eacute;n&eacute;ration de la liste.</label>
			<br/>
			<br/>
			<input type="button" value="continuer" onclick="ouvrir_field(this,'field_envoi',event);" />
		<?php
		}
		else{			
		?>
			<label for="adresse_suivi">Adresse de suivi&nbsp;:&nbsp;</label><input type="text" id="adresse_suivi" disabled="disabled" value="<?php echo $adresse_suivi; ?>" />
			<br/>
			<input type="radio" id="mail_true" name="mail" <?php if($mail_suivi) echo 'checked="checked"'; ?> disabled="disabled" /><label for="mail_true">&nbsp;m'envoyer un mail de suivi toutes les&nbsp;</label><select disabled="disabled" id="select_min" style="display:inline;"><option <?php if($seconde_suivi==120) echo 'selected="selected"'; ?> value="120">2</option><option <?php if($seconde_suivi==300) echo 'selected="selected"'; ?> value="300">5</option><option <?php if($seconde_suivi==600) echo 'selected="selected"'; ?> value="600">10</option><option <?php if($seconde_suivi==1800) echo 'selected="selected"'; ?> value="1800">30</option><option <?php if($seconde_suivi==3600) echo 'selected="selected"'; ?> value="3600">60</option></select><b>&nbsp;minutes.</b>
			<br/>
			<input type="radio" id="mail_false" name="mail" <?php if(!$mail_suivi) echo 'checked="checked"'; ?> disabled="disabled" /><label for="mail_false">&nbsp;ne pas m'envoyer de mail de suivi.</label>
			<br/>
			<br/>
			<input type="checkbox" <?php if($mail_fin) echo 'checked="checked"'; ?> id="envoi_fin" disabled="disabled" /><label for="envoi_fin">&nbsp;m'envoyer un mail lorsque l'envoi s'est termin&eacute;.</label>
			<br/>
			<br/>
		<?php
		}
		?>
	</li></fieldset>
	<br/>
	<fieldset id="field_envoi" style="<?php if(!$suivi) echo "display:none;"; else echo 'background-color:#e5eef2;'; ?>"><legend>D&eacute;but de l'envoi</legend><li id="zone_envoi">
		<?php
		if(!$envoi_commence){
		?>
		<div id="div_envoi">
			<input type="button" value="Débuter l'envoi" onclick="debuter_envoi(this,event);" style="width:95px;" />
			<div style="display:inline-block;" id="zone_barre_envoi"><div id="barre_envoi" style="width:600px;height:10px;background-image:url('img/fond_barre_chargement.png');display:inline-block;"><div id="avancement_envoi" style="width:1px;height:100%;background-image:url('img/fond_barre_avancement.gif');"></div></div><div id="avancement_envoi_p" style="display:inline-block;">0 %</div><div id="estimation_temps_envoi" style="width:100%;text-align:center;">&nbsp;</div></div>
		</div>
		<?php
		}
		else{
			//On calcul le nombre de pourcentage initial
			$pourcentage = (int)($nb_envoi*100/$nb_liste);
		?>
		<div id="div_envoi">
			<input type="button" value="Reprendre" onclick="debuter_envoi(this,event);" style="width:95px;" id="bout_d_envoi" />
			<div style="display:inline-block;" id="zone_barre_envoi"><div id="barre_envoi" style="width:600px;height:10px;background-image:url('img/fond_barre_chargement.png');display:inline-block;"><div id="avancement_envoi" style="width:<?php echo $pourcentage; ?>%;height:100%;background-image:url('img/fond_barre_avancement.gif');"></div></div><div id="avancement_envoi_p" style="display:inline-block;"><?php echo $pourcentage; ?> %</div><div id="estimation_temps_envoi" style="width:100%;text-align:center;">&nbsp;</div></div>
		</div>
		<?php
		}
		?>
	</li></fieldset>
	<br/>
</div>
<?php

include "../inc-footer.php";
}
else {
  $_SESSION['message'] .= "Erreur : veuillez sélectionner ".$cc_une." à envoyer.";  
  header("location:".$URLadmin."admin.php");
  exit();
}
 
?>