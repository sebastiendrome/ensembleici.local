<?php
//Ce fichier permet d'afficher l'accueil.
session_name("EspacePerso");
session_start();
require ('01_include/_var_ensemble.php');
require ('01_include/_connect.php');
if(!isset($_GET["p"])||empty($_GET["p"]))
	$_GET["p"] = "accueil";

include "01_include/select_agenda.php";
/************************************************
On récupère les informations dont on a besoin.
***/
//1. Edito
	//On récupère les 3 derniers édito
$requete_edito = "SELECT * FROM editorial ORDER BY date_creation DESC LIMIT 3";
$res_edito = $connexion->prepare($requete_edito);
$res_edito->execute();
$tab_edito = $res_edito->fetchAll();
$CONTENU_EDITORIAL = '<div id="zone_bloc_editorial_accueil" class="edito_1"><div id="zone_bloc_editorial_accueil_previous" onclick="diaporama_editorial_previous()"></div><div id="zone_bloc_editorial_accueil_next" onclick="diaporama_editorial_next()"></div>';
for($i=0;$i<count($tab_edito);$i++){
	$CONTENU_EDITORIAL .= '<div class="image 16/9"><img src="http://www.ensembleici.fr/00_dev_sam/'.$tab_edito[$i]["url_image"].'" /><div class="editorial_diapo_resume"><h3>'.$tab_edito[$i]["titre"].'</h3><div>'.substr($tab_edito[$i]["description"],0,300).'...</div></div></div>';
}
$CONTENU_EDITORIAL .= '</div>';



/*
$no_edito =1;
$requete_edito = "SELECT * FROM editorial WHERE no=:no";
$res_edito = $connexion->prepare($requete_edito);
$res_edito->execute(array(":no"=>$no_edito));
$tab_edito = $res_edito->fetchAll();
$TITRE_EDITORIAL = $tab_edito[0]["titre"];
$CONTENU_EDITORIAL = $tab_edito[0]["description"];*/
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="fr"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="fr"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="fr"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="fr"> <!--<![endif]-->
	<head>
		<!-- INFORMATIONS DE BASES -->
			<!-- Permet de regler l'affichage correctement sur smartphone -->
			<meta name="viewport" content="width=device-width, initial-scale=1.0, height=device-height, maximum-scale=1.0" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta http-equiv="content-language" content="fr" />
			<!-- Fin smartphone -->
			<link type="text/css" href="http://fonts.googleapis.com/css?family=Handlee" rel="stylesheet"></link>
			<link type="text/css" rel="stylesheet" href="http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:400,700"></link>
		<!-- FIN INFORMATIONS DE BASES -->
		<link rel="stylesheet" href="css/_css.css" type="text/css" />
		<!-- onload="if(typeof(regler_menu_smartphone)=='function'){regler_menu_smartphone(this);}" -->
		<link rel="stylesheet" href="css/_cssSmall.css" type="text/css" id="fichier_css" />
		<script type="text/javascript" src="js/_f.js"></script>
		<script type="text/javascript">
		var LARGEUR_MINIMUM = 1024;
		var LARGEUR_MINIMUM_MENU = 690;
		var TIMEOUT_RESIZE = false;
		var TIMEOUT_RESIZE_CONTENUS = false;
		var RAPPORT_IMAGE = 4/3;
		var PAGE_COURANTE = "<?php echo $_GET["p"]; ?>";
		
		function onload(){
			charge_diaporama();
			
			element("menuSmartphone_pages").style.maxHeight = (ecran()["y"]-5)+"px";
			element("menuSmartphone_pages").appendChild(element("menu"));
			
			resize(true);
			
			set_opacity(element("filtre"),0);
			setTimeout('element("filtre").style.display="none";',500);
		}
		
		function charge_diaporama(){
			//1. On récupère toutes les images du diaporama
			var les_images = new Array({"src":"img/diapo-index/diapo-11.jpg"},{"src":"img/diapo-index/diapo-13.jpg"},{"src":"img/diapo-index/diapo-12.jpg"},{"src":"img/diapo-index/diapo-14.jpg"},{"src":"img/diapo-index/diapo-15.jpg"});
			//2. On les places dans la div diaporama.
			var diaporama = element("diaporama");
			for(var i=0;i<les_images.length;i++){
				var img = document.createElement("img");
					img.src = les_images[i]["src"];
					img.alt = "Ensemble ici";
				diaporama.appendChild(img);
			}
			start_diaporama();
		}
		
		function start_diaporama(){
			//1. On réduit l'opacité du lastChild.
			set_opacity(element("diaporama").lastChild,0);
			//2. On passe le lastChild en firstChild
			setTimeout('element("diaporama").insertBefore(element("diaporama").lastChild,element("diaporama").firstChild);set_opacity(element("diaporama").firstChild,100)',1000);
			setTimeout('start_diaporama()',5000);
		}
		
		function resize(actif){
			if(typeof(actif)=="undefined")
				actif = false;
			if(TIMEOUT_RESIZE!=false){
				clearTimeout(TIMEOUT_RESIZE);
				TIMEOUT_RESIZE = false;
			}
			if(actif){
				var fichier_css_courant = element("fichier_css").href.substring((element("fichier_css").href.lastIndexOf("/")+1),element("fichier_css").href.length);
				if(ecran()["x"]<LARGEUR_MINIMUM_MENU){ //SmallCss
					if(fichier_css_courant!="_cssSmall.css"){
						element("fichier_css").onload = function(){regler_menu_smartphone(true)};
						element("fichier_css").href = "css/_cssSmall.css";
						element("menuSmartphone_pages").style.maxHeight = (ecran()["y"]-5)+"px";
						element("menuSmartphone_pages").appendChild(element("menu"));
					}
					else{
						regler_menu_smartphone(false);
					}
				}
				else{
					if(fichier_css_courant!="_cssBig.css"){
						element("page").insertBefore(element("menu"),element("contenu"));
						element("fichier_css").onload = function(){regler_menu_notSmartphone(true);};
						element("fichier_css").href = "css/_cssBig.css";
					}
					else{
						regler_menu_notSmartphone(false);
					}
				}
				
				resize_bloc();
			}
			else{
				LARGEUR_ECRAN = ecran()["x"];
				TIMEOUT_RESIZE = setTimeout("resize(true);",200);
			}
		}
		
		function regler_menu_smartphone(init){
			element("menuSmartphone_pages").style.width = (largeur("menuSmartphone")-largeur("menuSmartphone_utilisateur"))+"px";
			
			if(element("menu_retour")==null){
				var div = document.createElement("div");
					div.id = "menu_retour";
					div.className = "item_menu blanc";
					var div_ = document.createElement("div");
						div_.appendChild(document.createTextNode("Retour"));
					div.appendChild(div_);
				element("menu_home").firstChild.appendChild(document.createTextNode("Accueil"));
				element("menu").insertBefore(div,element("menu").firstChild);
				
				if(PAGE_COURANTE=="accueil")
					element("menu").style.top = -60+"px";
				else if(PAGE_COURANTE=="agenda")
					element("menu").style.top = -120+"px";
				else if(PAGE_COURANTE=="forum")
					element("menu").style.top = -180+"px";
				else if(PAGE_COURANTE=="structure")
					element("menu").style.top = -240+"px";
				else if(PAGE_COURANTE=="petiteannonce")
					element("menu").style.top = -300+"px";
				else if(PAGE_COURANTE=="editorial")
					element("menu").style.top = -360+"px";
				var les_items = element("menu").childNodes;
				for(var i=0;i<les_items.length;i++){
					if(typeof(les_items[i].className)!="undefined"&&dans_tab("item_menu",les_items[i].className.split(" "))&&les_items[i].id!="menu_home"){
						les_items[i].style.paddingRight = 0+"px";
						les_items[i].style.paddingLeft = 0+"px";
					}
				}
				menu_site_smartphone(true);
			}
			
			
			
			element("fleche_deplier").style.left = ((largeur(element("fleche_deplier").parentNode)/2)-largeur("fleche_deplier")/2)+"px";
			if(largeur("menuSmartphone")>LARGEUR_MINIMUM_MENU){
				//Il faut alors sortir la css_normal
				resize(true);
			}
		}
		
		function regler_menu_notSmartphone(init){
			if(element("menu_retour")!=null){
				element("menu_retour").parentNode.removeChild(element("menu_retour"));
				element("menu_home").firstChild.removeChild(element("menu_home").firstChild.lastChild);
				element("menu").style.top = 0+"px";
			}
				
				
			//var t_max = largeur("menu")-largeur("menu_home")-10; //10 = 2*5 item (bordures)
			var largeur_tt_menu = 0;
			var les_items = element("menu").childNodes;
			var nb_menu_reglage = 0;
			for(var i=0;i<les_items.length;i++){
				if(typeof(les_items[i].className)!="undefined"&&dans_tab("item_menu",les_items[i].className.split(" "))){
					largeur_tt_menu += largeur(les_items[i]); //2 : largeur de bordure
					if(les_items[i].id!="menu_home"){
						nb_menu_reglage++;
						var padding_item = ((typeof(les_items[i].style.paddingLeft)!="undefined"&&les_items[i].style.paddingLeft!=""&&les_items[i].style.paddingLeft!="none")?(parseInt(les_items[i].style.paddingLeft)):0)+((typeof(les_items[i].style.paddingRight)!="undefined"&&les_items[i].style.paddingRight!=""&&les_items[i].style.paddingRight!="none")?(parseInt(les_items[i].style.paddingRight)):0);
						largeur_tt_menu -= padding_item;
					}
				}
			}
			LARGEUR_MINIMUM_MENU = largeur_tt_menu;
			var taille_restante = largeur("menu")-LARGEUR_MINIMUM_MENU;
			if(taille_restante<0){
				//Il faut alors sortir la css_smartphone (690 n'est en fait pas la taille minimum)
				resize(true);
			}
			else{
				var new_padding = Math.floor(taille_restante/nb_menu_reglage/2);
					var pix_restant = taille_restante-(new_padding*nb_menu_reglage*2);
						pix_restant_l = Math.floor(pix_restant/2);
						pix_restant_r = Math.ceil(pix_restant/2);
				for(var i=0;i<les_items.length;i++){
					if(typeof(les_items[i].className)!="undefined"&&dans_tab("item_menu",les_items[i].className.split(" "))&&les_items[i].id!="menu_home"){
						add_left = (i!=les_items.length-1)?0:pix_restant_l;
						add_right = (i!=les_items.length-1)?0:pix_restant_r;
						les_items[i].style.paddingRight = new_padding+add_right+"px";
						les_items[i].style.paddingLeft = new_padding+add_left+"px";
					}
				}
			}
			
			/*
			var taille_agenda = Math.floor(16*t_max/100);
			var taille_forum = Math.floor(16*t_max/100);
			var taille_structure = Math.floor(21*t_max/100);
			var taille_petiteannonce = Math.floor(30*t_max/100);
			var taille_editorial = Math.floor(17*t_max/100);
			
			var taille_total = taille_agenda+taille_forum+taille_structure+taille_petiteannonce+taille_editorial;
			var dif = t_max-taille_total;
			
			console.log(dif);
			
			element("menu_agenda").style.width = taille_agenda+"px";
			element("menu_forum").style.width = taille_forum+"px";
			element("menu_structure").style.width = taille_structure+"px";
			element("menu_petiteannonce").style.width = (taille_petiteannonce+dif)+"px";
			element("menu_editorial").style.width = taille_editorial+"px"; */
		}
		
		function menu_site_smartphone(laisser_ferme){
			if(typeof(laisser_ferme)=="undefined")
				laisser_ferme = false;
			if(!dans_tab("ouvert",element("menuSmartphone_pages").className.split(" "))&&!laisser_ferme){
				element("filtre").style.display = "block";
				set_opacity(element("filtre"),100);
				element("menuSmartphone").style.zIndex = 102;
			
				element("menuSmartphone_pages").style.height = hauteur("menu")+"px";
				element("menu").style.top = 0+"px";
				element("menuSmartphone_pages").className = element("menuSmartphone_pages").className+" ouvert";
				element("menuSmartphone_pages").style.width = (largeur("menuSmartphone")-largeur("menuSmartphone_utilisateur"))+"px";
			}
			else{
				set_opacity(element("filtre"),0);
				element("filtre").style.display = "none";
				element("menuSmartphone").style.zIndex = 100;
			
				element("menuSmartphone_pages").style.height = 60+"px";
				element("menuSmartphone_pages").className = element("menuSmartphone_pages").className.replace(" ouvert","");
				element("menuSmartphone_pages").style.width = (largeur("menuSmartphone")-largeur("menuSmartphone_utilisateur"))+"px";
				
				
				if(PAGE_COURANTE=="accueil")
					element("menu").style.top = -60+"px";
				else if(PAGE_COURANTE=="agenda")
					element("menu").style.top = -120-1+"px"; //-1 bordure haut
				else if(PAGE_COURANTE=="forum")
					element("menu").style.top = -180-3+"px"; //-1 bordure haut -2 bordure précédent
				else if(PAGE_COURANTE=="structure")
					element("menu").style.top = -240-5+"px"; //-1 bordure haut -2*nb_item_precedent bordure précédent
				else if(PAGE_COURANTE=="petiteannonce")
					element("menu").style.top = -300-7+"px";
				else if(PAGE_COURANTE=="editorial")
					element("menu").style.top = -360-9+"px";
			}
		}
		
		function choix_page(el){
			PAGE_COURANTE = el.id.split("_")[1];
		}
		
		function resize_bloc(){
			//On récupère les lignes
			console.log("---------------------------------------------------------");
			var lignes = element("contenu_page").getElementsByClassName("row");
			for(var i=0;i<lignes.length;i++){
				var largeur_ligne = largeur(lignes[i]);
					if(largeur_ligne%2!=0)
						largeur_ligne++;
				var largeur_blocs_ligne = 0;
				if(dans_tab("multi_row",lignes[i].className.split(" ")))
					lignes[i].className = lignes[i].className.replace(" multi_row","");
				//On récupère les blocs de chaque ligne
				var blocs = lignes[i].getElementsByClassName("bloc");
				for(var j=0;j<blocs.length;j++){
					//On calcul la taille des blocs
					largeur_blocs_ligne += largeur(blocs[j]);
					console.log("bloc: "+j+" -> "+largeur(blocs[j]));
					console.log("somme: "+j+" -> "+largeur_blocs_ligne);
				}
				console.log(i+" -> "+largeur_blocs_ligne+" - "+largeur_ligne);
				if(largeur_blocs_ligne>largeur_ligne)
					lignes[i].className = lignes[i].className+" multi_row";
			}
			if(TIMEOUT_RESIZE_CONTENUS!=false){
				clearTimeout(TIMEOUT_RESIZE_CONTENUS);
				TIMEOUT_RESIZE_CONTENUS = false;
			}
			TIMEOUT_RESIZE_CONTENUS = setTimeout("reajuste_tous_contenus();",200);
		}
		
		function reajuste_tous_contenus(){
			// Pour la page d'accueil
			if(element("edito")!=null){
				var zone = element("zone_bloc_editorial_accueil");
					zone.style.height = Math.floor(largeur(zone)*9/16)+"px";
			}
			if(element("agenda")!=null){
				var zone = element("zone_bloc_evenement_accueil");
				//alert(zone);
				var larg_max = largeur(zone);
				//var larg_bloc_min = parseInt(getStyle(zone.firstChild,"min-width"))+parseInt(getStyle(zone.firstChild.nextSibling,"min-width"))+parseInt(getStyle(zone.lastChild,"min-width"));
				//var larg_bloc_max = parseInt(getStyle(zone.firstChild,"max-width"))+parseInt(getStyle(zone.firstChild.nextSibling,"max-width"))+parseInt(getStyle(zone.lastChild,"max-width"));
				//console.log(" -- largeurs : "+larg_max+" : "+larg_bloc_min+" < "+larg_bloc_max);
				if(dans_tab("zone_petite",zone.className.split(" "))){
					//var larg_bloc_min = parseInt(getStyle(zone.firstChild,"min-width"));
					var larg_bloc_max = parseInt(getStyle(zone.firstChild,"max-width"));
					if(larg_bloc_max<larg_max){ //On peut changer de format.
						if(larg_max>=500){ //Grand
							console.log("On passe de petit à grand");
							zone.className = zone.className.replace("zone_petite","zone_grande");
						}
						else{ //Moyen
							console.log("On passe de petit à moyen");
							zone.className = zone.className.replace("zone_petite","zone_moyenne");
						}
					}
				}
				else if(dans_tab("zone_moyenne",zone.className.split(" "))){
					var larg_bloc_min = parseInt(getStyle(zone.lastChild,"min-width"));
					var larg_bloc_max = parseInt(getStyle(zone.lastChild,"max-width"));
					if(larg_bloc_min>larg_max){ //On passe au petit
						console.log("On passe de moyen à petit");
						zone.className = zone.className.replace("zone_moyenne","zone_petite");
					}
					else{
						if(larg_bloc_max<larg_max){ //On passe au grand
							console.log("On passe de moyen à grand");
							zone.className = zone.className.replace("zone_moyenne","zone_grande");
						}
					}
				}
				else if(dans_tab("zone_grande",zone.className.split(" "))){
					var larg_bloc_min = parseInt(getStyle(zone.firstChild,"min-width"))+parseInt(getStyle(zone.firstChild.nextSibling,"min-width"))+parseInt(getStyle(zone.lastChild,"min-width"));
					if(larg_max<larg_bloc_min){ //On peut changer de format
						if(larg_max<300){ //Petit
							console.log("On passe de grande à petit");
							zone.className = zone.className.replace("zone_grande","zone_petite");
						}
						else{ //Moyen
							console.log("On passe de grande à moyen");
							zone.className = zone.className.replace("zone_grande","zone_moyenne");
						}
					}
				}
			}
			
			//1. les images
			var imgs = element("contenu_page").getElementsByTagName("img");
			for(var i=0;i<imgs.length;i++){
				if(dans_tab("image",imgs[i].parentNode.className.split(" "))){
					img_load(imgs[i]);
				}
			}
		}
		
		function img_load(img){
			if(img.style.position!="relative")
				img.style.position = "relative";
			
			if(dans_tab("4/3",img.parentNode.className.split(" ")))
				var rapport_courant = 4/3;
			else if(dans_tab("16/9",img.parentNode.className.split(" ")))
				var rapport_courant = 16/9;
			else
				var rapport_courant = RAPPORT_IMAGE;
	
			var x_max = largeur(img.parentNode); //Largeur du cadre
			var y_max = Math.floor(x_max/rapport_courant); //Nouvelle hauteur du cadre
			img.parentNode.style.height = y_max +"px";
	
			//On centre maintenant l'image dans ce cadre
			var x = img.width;
			var y = img.height;
	
			var new_x = x_max;
			var new_y = Math.floor(new_x*y/x);
			if(new_y<y_max){
				new_y = y_max;
				new_x = Math.floor(new_y*x/y);
			}
	
			img.style.width = new_x+"px";
			img.style.height = new_y+"px";
			img.style.left = Math.floor(x_max/2-new_x/2)+"px";
			img.style.top = Math.floor(y_max/2-new_y/2)+"px";
		}
		
		function input_focus(input){
			if(input.value==input.title){
				input.value = "";
				input.className = input.className.replace(" vide","");
			}
		}
		function input_blur(input){
			if(input.value==input.title||input.value==""){
				input.className = input.className+" vide";
				input.value = input.title;
			}
		}
		
		
		/*function diaporama_editorial(){
		
		}*/
		
		function diaporama_editorial_next(){
			console.log("droite");
			var zone = element("zone_bloc_editorial_accueil");
			if(zone.className=="edito_1"){
				zone.className = "edito_2";
			}
			else if(zone.className=="edito_2"){
				zone.className = "edito_3";
			}
		}
		function diaporama_editorial_previous(){
			console.log("gauche");
			var zone = element("zone_bloc_editorial_accueil");
			if(zone.className=="edito_2"){
				zone.className = "edito_1";
			}
			else if(zone.className=="edito_3"){
				zone.className = "edito_2";
			}
		}
		</script>
		<style type="text/css">
			.row{
				display: block;
				width: 100%;
			}
			.row .bloc{
				display: inline-block;
				text-align: center;
				vertical-align: top;
			}
			.bloc>div{
				padding: 10px;
				margin: 20px;
			}
			.bloc.no_padding>div{
				padding: 0px;
				margin: 20px;
			}
			.bloc + .bloc>div{
				margin-left: 0px;
			}
			.bloc>div>div{
				width: 100%;
				position: relative;
			}
			.bloc.grand{
				width: 100%;
				max-width: 2048px; /* none ? */
				/*min-width: 800px;*/
			}
			.bloc.grand_moyen{
				width: 67%;
				max-width: 1373px;
				/*min-width: 400px;*/
			}
			.bloc.moyen{
				width: 50%;
				max-width: 512px;
				min-width: 200px;
			}
			.bloc.moyen_petit{
				width: 33%;
				/*max-width: 683px;*/
				min-width: 200px;
			}
			
			.row.multi_row .bloc.grand, .row.multi_row .bloc.grand_moyen, .row.multi_row .bloc.moyen, .row.multi_row .bloc.moyen_petit{
				width: 100%;
			}
			.row.multi_row .bloc + .bloc>div{
				margin: 20px;
				margin-top: 0px;
			}
			
			.bloc.gris>div{
				border: 1px solid rgb(227, 214, 199);
				background-color: rgb(240, 237, 234);
				border-radius: 3px;
			}
			
			.bloc.bleu>div{
				/*border: 1px solid rgb(45, 171, 218);
				border: 1px solid rgba(43, 151, 191,0.5);*/
				border: 1px solid rgba(43, 151, 191,0.1);
				background-color: rgba(35, 170, 221,0.05);
				box-shadow: 0px 0px 20px rgba(35, 170, 221,0.1) inset;
				/*background-color: rgb(229, 238, 242);*/
				border-radius: 3px;
			}
			.bloc.bleu h1{
				color: rgb(45, 171, 218);
			}
			
			.bloc.vert>div{
				border: 1px solid rgba(90,160,90,0.3);
				background-color: rgba(71,171,76,0.2);
				border-radius: 3px;
				
				background-image: -webkit-gradient(
					linear,
					left top, left bottom,
					from(rgba(90,160,90,0.2)),
					to(rgba(90,160,90,0)));
				background-image: -webkit-linear-gradient(
					top,
					rgba(90,160,90,0.2),
					rgba(90,160,90,0) 100%);
				background-image: -moz-linear-gradient(
					top,
					rgba(90,160,90,0.2),
					rgba(90,160,90,0) 100%);
				background-image: -o-linear-gradient(
					top,
					rgba(90,160,90,0.2),
					rgba(90,160,90,0) 100%);
				background-image: linear-gradient(
					top,
					rgba(78,78,78,0.2),
					rgba(90,160,90,0) 100%);
			}
			
			.bloc.rouge>div{
				border: 1px solid rgba(197, 78, 44, 1);
				background-color: rgba(233, 91, 42, 1);
				border-radius: 3px;
				
				/*background-image: -webkit-gradient(
					linear,
					left top, left bottom,
					from(rgba(197, 78, 44, 0.2)),
					to(rgba(197, 78, 44, 0)));
				background-image: -webkit-linear-gradient(
					top,
					rgba(197, 78, 44, 0.2),
					rgba(197, 78, 44, 0) 100%);
				background-image: -moz-linear-gradient(
					top,
					rgba(197, 78, 44, 0.2),
					rgba(197, 78, 44, 0) 100%);
				background-image: -o-linear-gradient(
					top,
					rgba(197, 78, 44, 0.2),
					rgba(197, 78, 44, 0) 100%);
				background-image: linear-gradient(
					top,
					rgba(197, 78, 44, 0.2),
					rgba(197, 78, 44, 0) 100%);*/
				background-image: -webkit-gradient(
					linear,
					left top, left bottom,
					from(rgba(197, 78, 44, 1)),
					to(rgba(197, 78, 44, 0)));
				background-image: -webkit-linear-gradient(
					top,
					rgba(197, 78, 44, 1),
					rgba(197, 78, 44, 0) 100%);
				background-image: -moz-linear-gradient(
					top,
					rgba(197, 78, 44, 1),
					rgba(197, 78, 44, 0) 100%);
				background-image: -o-linear-gradient(
					top,
					rgba(197, 78, 44, 1),
					rgba(197, 78, 44, 0) 100%);
				background-image: linear-gradient(
					top,
					rgba(197, 78, 44, 1),
					rgba(197, 78, 44, 0) 100%);
			}
			
			.bloc.pomme>div{
				border: 1px solid rgba(184, 185, 55, 1);
				background-color: rgba(215, 218, 46,1);
				border-radius: 3px;
				
				/*background-image: -webkit-gradient(
					linear,
					left top, left bottom,
					from(rgba(184, 185, 55, 0.2)),
					to(rgba(184, 185, 55, 0)));
				background-image: -webkit-linear-gradient(
					top,
					rgba(184, 185, 55, 0.2),
					rgba(184, 185, 55, 0) 100%);
				background-image: -moz-linear-gradient(
					top,
					rgba(184, 185, 55, 0.2),
					rgba(184, 185, 55, 0) 100%);
				background-image: -o-linear-gradient(
					top,
					rgba(184, 185, 55, 0.2),
					rgba(184, 185, 55, 0) 100%);
				background-image: linear-gradient(
					top,
					rgba(184, 185, 55, 0.2),
					rgba(184, 185, 55, 0) 100%);*/
				background-image: -webkit-gradient(
					linear,
					left top, left bottom,
					from(rgba(184, 185, 55,1)),
					to(rgba(184, 185, 55, 0)));
				background-image: -webkit-linear-gradient(
					top,
					rgba(184, 185, 55, 1),
					rgba(184, 185, 55, 0) 100%);
				background-image: -moz-linear-gradient(
					top,
					rgba(184, 185, 55, 1),
					rgba(184, 185, 55, 0) 100%);
				background-image: -o-linear-gradient(
					top,
					rgba(184, 185, 55,1),
					rgba(184, 185, 55, 0) 100%);
				background-image: linear-gradient(
					top,
					rgba(184, 185, 55, 1),
					rgba(184, 185, 55, 0) 100%);
			}
			
			.bloc.orange>div{
				border: 1px solid rgba(215, 153, 66,1);
				background-color: rgba(246, 174, 72,1);
				border-radius: 3px;
				/*
				background-image: -webkit-gradient(
					linear,
					left top, left bottom,
					from(rgba(215, 153, 66, 0.2)),
					to(rgba(215, 153, 66, 0)));
				background-image: -webkit-linear-gradient(
					top,
					rgba(215, 153, 66, 0.2),
					rgba(215, 153, 66, 0) 100%);
				background-image: -moz-linear-gradient(
					top,
					rgba(215, 153, 66, 0.2),
					rgba(215, 153, 66, 0) 100%);
				background-image: -o-linear-gradient(
					top,
					rgba(215, 153, 66, 0.2),
					rgba(215, 153, 66, 0) 100%);
				background-image: linear-gradient(
					top,
					rgba(215, 153, 66, 0.2),
					rgba(215, 153, 66, 0) 100%);*/
				background-image: -webkit-gradient(
					linear,
					left top, left bottom,
					from(rgba(215, 153, 66, 1)),
					to(rgba(215, 153, 66, 0)));
				background-image: -webkit-linear-gradient(
					top,
					rgba(215, 153, 66, 1),
					rgba(215, 153, 66, 0) 100%);
				background-image: -moz-linear-gradient(
					top,
					rgba(215, 153, 66, 1),
					rgba(215, 153, 66, 0) 100%);
				background-image: -o-linear-gradient(
					top,
					rgba(215, 153, 66, 1),
					rgba(215, 153, 66, 0) 100%);
				background-image: linear-gradient(
					top,
					rgba(215, 153, 66, 1),
					rgba(215, 153, 66, 0) 100%);
			}
			
			/*.titre_bloc{
				margin: 0;
				padding: 0;
				position: absolute;
				top: 0px;
				left: 0px;
				color: white;
				font-size: 1.5em;
				-webkit-transform-origin: 0% 0%;
				   -moz-transform-origin: 0% 0%;
					-ms-transform-origin: 0% 0%;
					 -o-transform-origin: 0% 0%;
						transform-origin: 0% 0%;
				-webkit-transform: rotate(-90deg);
				   -moz-transform: rotate(-90deg);
					-ms-transform: rotate(-90deg);
					 -o-transform: rotate(-90deg);
						transform: rotate(-90deg);
			}*/
			
			.titre_bloc{
				background-image: url("img/titres-blocs-home.png");
				background-repeat: no-repeat;
				width: 34px;
				position: absolute;
				top: -15px;
				left: -50px;
			}
			#edito .titre_bloc{
				background-position: 0px 0px;
				height: 85px;
			}
			
			#agenda .titre_bloc{
				background-position: 0px 85px;
				height: 110px;
			}
			
			/**********************************
				LES INPUTS
				**/

				span.span_input{
					padding: 0.5em 1em;
					text-shadow: 0px 1px 0px #FFF;
					cursor: pointer;
					display: inline-block;
					text-align: center;
					font-weight: 300;
					font-family: "Source Sans Pro", "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
					border: 1px hidden;
					text-decoration: underline;
				}

				span.span_input_active{
					padding: 0.5em 1em;
					text-shadow: 0px 1px 0px #FFF;
					cursor: pointer;
					display: inline-block;
					text-align: center;
					font-weight: 300;
					font-family: "Source Sans Pro", "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
					border: 1px solid #E1E1E1;
					background-color: #F1F1F1;
					background-image: -moz-linear-gradient(center top , #F5F5F5, #F1F1F1);
				}

				input[type="button"], input[type="submit"], button, span.span_input:hover, select{
					background-color: #F5F5F5;
					background-image: -moz-linear-gradient(center top , #F9F9F9, #F5F5F5);
					border: 1px solid #E5E5E5;
					padding: 0.5em 1em;
					text-shadow: 0px 1px 0px #FFF;
					cursor: pointer;
					display: inline-block;
					text-align: center;
					font-weight: 300;
					font-family: "Source Sans Pro", "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
					font-family: 'Josefin Sans', sans-serif;
					text-decoration: none;
				}

				input[type="button"].vert, input[type="submit"].vert, button.vert{
					background-color: #437737;
					background-image: -moz-linear-gradient(center top , #589E47, #437737);
					border: 0px none;
					border-radius: 2px;
					color: white;
					text-shadow: 0px -1px 0px rgba(0, 0, 0, 0.08);
				}
				input[type="button"]:hover, input[type="submit"]:hover, button:hover, .ligne_recherche:hover input[type="button"].over_ligne, .ligne_recherche:hover button.over_ligne, select:hover{
					background-color: #F1F1F1;
					background-image: -moz-linear-gradient(center top , #F5F5F5, #F1F1F1);
					border-color: #E1E1E1;
				}
				input[type="button"].vert:hover, input[type="submit"].vert:hover, button.vert:hover{
					background-color: #437737;
					background-image: -moz-linear-gradient(center top , #539443, #437737);
				}

				input[type="text"], textarea{
					border: 1px solid #E5E5E5;
					padding: 0.5em 1em;
					text-shadow: 0px 1px 0px #FFF;
					display: inline-block;
					text-align: left;
					font-weight: 300;
					font-family: "Source Sans Pro", "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
				}

				input[type="text"]:focus, textarea:focus{
					box-shadow: 0px 0px 10px rgba(87,96,60,0.2);/*rgba(87,96,60,0.2);*/
				}

				textarea{
					width: 100%;
					height: auto;
					resize: none;
					height: 10em;
				}

				input[type="text"].vide, textarea.vide{
					font-style: italic;
					color: #E4E4E4;
				}
				
				input[type="text"].recherche{
					background-image: url("img/img_colorize.php?uri=ico_recherche.png&c=f0edea");
					background-repeat: no-repeat;
					background-position: 5px center;
					text-indent: 35px;
					padding-left: 0px;
					margin: 0.5em;
					min-width: 140px;
				}
				
			/*.item_menu>span{
				position: absolute;
				font-size: 0.7em;
				display: inline-block;
				width: 100%;
				text-align: center;
				top: 10px;
			}*/
			
			
			.bloc_evenement_accueil+.bloc_evenement_accueil>div{
				margin-left: 10px;
			}
			
			.bloc_evenement_accueil{
				display: inline-block;
				overflow: hidden;
				position: relative;
				text-align: center;
				vertical-align: top;
			}
			
			/**
				Bloc événement petit ecran
				**/
			.zone_petite .bloc_gauche, .zone_petite .bloc_milieu, .zone_petite .bloc_droite{
				min-width: 200px;
				max-width: 300px;
				width: 100%;
			}
				.zone_petite .bloc_milieu>div{
					width: 50%;
					display: inline-block;
				}
				.zone_petite .bloc_droite>div, .zone_petite .bloc_gauche>div{
					width: 100%;
				}
			
			/**
				Bloc événement ecran moyen
				**/
			.zone_moyenne .bloc_gauche{
				min-width: 200px;
				max-width: 300px;
				width: 60%;
			}
			.zone_moyenne .bloc_milieu{
				min-width: 100px;
				max-width: 200px;
				width: 40%;
			}
			.zone_moyenne .bloc_droite{
				min-width: 300px;
				max-width: 500px;
				width: 100%;
			}
				.zone_moyenne .bloc_droite>div, .zone_moyenne .bloc_gauche>div, .zone_moyenne .bloc_milieu>div{
					width: 100%;
				}
			/**
				Bloc événement grand ecran
				**/
			.zone_grande .bloc_gauche, .zone_grande .bloc_droite{
				min-width: 200px;
				max-width: none;
				width: 40%;
			}
			.zone_grande .bloc_milieu{
				min-width: 100px;
				max-width: none;
				width: 20%;
			}
				.zone_grande .bloc_droite>div, .zone_grande .bloc_gauche>div, .zone_grande .bloc_milieu>div{
					width: 100%;
				}
				
				/*
				.bloc_60{
					width: 60%;
					min-width: 300px;
				}
				.bloc_40{
					width: 40%;
					min-width: 200px;
				}
				.bloc_20{
					width: 20%;
					min-width: 100px;
				}
				
				
				.bloc_20>div+div{
					margin-top: 10px;
				}
			
			.bloc_date{
				position: absolute;
			}*/
			
			.image{
				position: relative;
				overflow: hidden;
			}
			
			#zone_bloc_editorial_accueil{
				overflow: hidden;
				white-space: nowrap;
			}
				#zone_bloc_editorial_accueil>div.image{
					display: inline-block;
					white-space: normal;
					width: 100%;
					position: relative;
					-webkit-transition: left 500ms ease-out;
					-moz-transition: left 500ms ease-out;
					-o-transition: left 500ms ease-out;
					-ms-transition: left 500ms ease-out;
					transition: left 500ms ease-out;
				}
					#zone_bloc_editorial_accueil.edito_1>div.image{
						left:0px;
					}
					#zone_bloc_editorial_accueil.edito_2>div.image{
						left:-100%;
					}
					#zone_bloc_editorial_accueil.edito_3>div.image{
						left:-200%;
					}
					
			.editorial_diapo_resume{
				background-color: white;
				position: absolute;
				bottom: 0px;
				left: 0px;
				text-align: justify;
				padding: 2em;
				padding-top: 0em;
				background-color: rgba(255,255,255,0.8);
				max-height: 3em;
				
				-webkit-transition: background 500ms ease-out, max-height 500ms ease-out;
				-moz-transition: background 500ms ease-out, max-height 500ms ease-out;
				-o-transition: background 500ms ease-out, max-height 500ms ease-out;
				-ms-transition: background 500ms ease-out, max-height 500ms ease-out;
				transition: background 500ms ease-out, max-height 500ms ease-out;
			}
			#zone_bloc_editorial_accueil:hover .editorial_diapo_resume{/* #zone_bloc_editorial_accueil>div:hover .editorial_diapo_resume{ */
				background-color: rgba(255,255,255,0.9);
				max-height: 50%;
			}
			
			#zone_bloc_editorial_accueil .editorial_diapo_resume>div{
				opacity: 0;
				-webkit-transition: opacity 500ms ease-out;
				-moz-transition: opacity 500ms ease-out;
				-o-transition: opacity 500ms ease-out;
				-ms-transition: opacity 500ms ease-out;
				transition: opacity 500ms ease-out;
			}
			
			#zone_bloc_editorial_accueil:hover .editorial_diapo_resume>div{/* #zone_bloc_editorial_accueil>div:hover .editorial_diapo_resume>div{ */
				opacity: 1;
			}
			
			.editorial_diapo_resume>h3{
				text-align: left;
				color: rgb(35, 170, 221);
				font-weight: normal;
				font-size: 1.5em;
				text-shadow: 1px 0px 0px rgba(0, 0, 0, 0.1), 0px 1px 0px rgba(255, 255, 255, 0.1);
			}
			
			#zone_bloc_editorial_accueil_previous, #zone_bloc_editorial_accueil_next{
				position: absolute;
				height: 50px;
				width: 50px;
				top: 40%;
				z-index: 1;
				background-repeat: no-repeat;
				background-position: center center;
				cursor: pointer;
				
				-webkit-transition: opacity 200ms ease-out, transform 200ms ease-out;
				-moz-transition: opacity 200ms ease-out, transform 200ms ease-out;
				-o-transition: opacity 200ms ease-out, transform 200ms ease-out;
				-ms-transition: opacity 200ms ease-out, transform 200ms ease-out;
				transition: opacity 200ms ease-out, transform 200ms ease-out;
			}
			#zone_bloc_editorial_accueil_previous:hover, #zone_bloc_editorial_accueil_next:hover{
				-webkit-transform: scale(1.4,1.4);
				-moz-transform: scale(1.4,1.4);
				-o-transform: scale(1.4,1.4);
				-ms-transform: scale(1.4,1.4);
				transform: scale(1.4,1.4);
			}
			#zone_bloc_editorial_accueil_next{right: 0px;background-image:url("img/btn_next.png");}
			#zone_bloc_editorial_accueil_previous{left: 0px;background-image:url("img/btn_previous.png");}
			#zone_bloc_editorial_accueil.edito_3>#zone_bloc_editorial_accueil_next{opacity: 0;pointer-events:none;}
			#zone_bloc_editorial_accueil.edito_1>#zone_bloc_editorial_accueil_previous{opacity: 0;pointer-events:none;}
			
		</style>
	</head>
	<?php
		//$contenu_editorial = '<h1>'.$TITRE_EDITORIAL.'</h1>'.$CONTENU_EDITORIAL;
		$contenu_forum = '<h1 class="vert">Ensembleici fait peau neuve !</h1><p>Ensembleici vous accueille aujourd\'hui sur sa nouvelle version.</p><p>Un nouveau menu vous permet de vous deplacer auisément entre les différentes parties du site.</p><p>Un éditorial est désormais en ligne, vivez l\'actualité autour de chez vous!</p><p>Le site est maintenant compatible tablettes et mobiles !</p>';
	
		$ligne1 = array(array("class"=>"grand_moyen bleu no_padding","titre"=>false,"id"=>"edito","contenu"=>$CONTENU_EDITORIAL),array("class"=>"moyen_petit","contenu"=>$contenu_forum));
		$ligne2 = array(array("class"=>"grand rouge","titre"=>true,"id"=>"agenda","contenu"=>afficher_evenement_accueil(select_evenements(9568))));
		$ligne3 = array(array("class"=>"moyen pomme","contenu"=>"Ici les derniers petites annonces"),array("class"=>"moyen orange","contenu"=>"<p>Ceci est le texte qui sera remplacé par le répertoire</p><p></p><p></p><p>Et ouais gros!</p>"));
		$lignes = array($ligne1,$ligne2,$ligne3);	
	?>
	<body onload="onload()" onresize="resize()">
		<div id="filtre"></div>
		<div id="menuSmartphone"><div id="menuSmartphone_utilisateur"><img src="http://www.sudplanete.net/_admin/africine_dev/03_interface/img_colorize.php?uri=ico_headerMenuSmartphone.png&c=b4b4b4" /></div><div id="menuSmartphone_pages" onclick="menu_site_smartphone()"><div id="fleche_deplier"></div></div></div>
		<div id="page"><!-- <img width="693" height="183" alt="Ensemble ici" src="img/diapo-index/diapo-11.jpg" style="position: absolute; top: 0px; z-index: 5; opacity: 0; display: none;"></img> -->
			<div id="header">
				<div id="logo"><img src="img/logo-ensembleici.png" alt="Ensemble ici" /></div>
				<div id="bandeau">
					<div id="diaporama"></div>
					<div id="cache"><img width="322" height="183" alt="Ensemble ici" src="img/bandeau-cache.png"></img></div>
				</div>
				<div id="slogan"><img alt="Tous acteurs de la vie locale" src="img/bandeau-slogan.png" /></div>
			</div>
			<!--<div id="menu">
				<a id="menu_home" class="item_menu gris" href="index.php?p=accueil" onclick="choix_page(this)"><div></div>
				</a><a id="menu_agenda" class="item_menu rouge" href="index.php?p=agenda" onclick="choix_page(this)"><div>agenda</div>
				</a><a id="menu_forum" class="item_menu bleu" href="index.php?p=forum" onclick="choix_page(this)"><div>forums</div>
				</a><a id="menu_structure" class="item_menu vert" href="index.php?p=structure" onclick="choix_page(this)"><div>structures</div>
				</a><a id="menu_petiteannonce" class="item_menu pomme" href="index.php?p=petiteannonce" onclick="choix_page(this)"><div>petites annonces</div>
				</a><a id="menu_editorial" class="item_menu orange" href="index.php?p=editorial" onclick="choix_page(this)"><div>éditoriel</div></a></div>-->
				<div id="menu">
				<a id="menu_home" class="item_menu gris" href="index.php?p=accueil" onclick="choix_page(this)"><div></div>
				</a><a id="menu_agenda" class="item_menu bleu" href="index.php?p=edito" onclick="choix_page(this)"><div>&Eacute;ditorial</div>
				</a><a id="menu_forum" class="item_menu rouge" href="index.php?p=forum" onclick="choix_page(this)"><div>Agenda</div>
				</a><a id="menu_petiteannonce" class="item_menu pomme" href="index.php?p=petiteannonce" onclick="choix_page(this)"><div>annonces</div>
				</a><a id="menu_structure" class="item_menu orange" href="index.php?p=structure" onclick="choix_page(this)"><div>R&eacute;pertoire</div>
				</a><a id="menu_editorial" class="item_menu vert" href="index.php?p=editorial" onclick="choix_page(this)"><div>Forums</div></a></div>
			<div id="contenu"><div>
				<div id="barressmenu"><div><h1 id="h1ville">nyons</h1><input type="button" class="ico map" value="Modifier" /></div><div><input type="button" value="Inscrivez-vous à notre lettre d'information" /><input id="recherche" class="recherche vide" type="text" name="recherche" value="Recherche" title="Recherche" maxlength="100" onfocus="input_focus(this);" onblur="input_blur(this);" /></div></div>
				<div id="contenu_page"><?php
				for($i=0;$i<count($lignes);$i++){
					echo '<div class="row">';
					for($j=0;$j<count($lignes[$i]);$j++){
						if($lignes[$i][$j]["titre"]!=null&&$lignes[$i][$j]["titre"]){
							$titre_bloc = '<div class="titre_bloc"></div>';
							$padding_left = ' style="padding-left:60px;"';
						}
						else{
							$titre_bloc = '';
							$padding_left = '';	
						}
						echo '<div class="bloc '.$lignes[$i][$j]["class"].'" id="'.$lignes[$i][$j]["id"].'"><div'.$padding_left.'><div>'.$titre_bloc.$lignes[$i][$j]["contenu"].'</div></div>';
						echo '</div>';
					}
					echo '</div>';
				}
				?></div>
				<div></div>
			</div></div>
		</div>
	</body>
</html>
