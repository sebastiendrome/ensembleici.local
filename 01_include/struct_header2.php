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
			<link type="text/css" href="http://fonts.googleapis.com/css?family=Handlee" rel="stylesheet" />
			<link type="text/css" rel="stylesheet" href="http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:400,700" />
		<!-- FIN INFORMATIONS DE BASES -->
		<link rel="stylesheet" href="css/_css.new.css" type="text/css" />
		
		<link rel="stylesheet" href="css/_cssHome.css" type="text/css" />
		
		
		<link rel="stylesheet" type="text/css" href="css/_msg.css" />
		<?php
			$ua = $_SERVER['HTTP_USER_AGENT'];
			if (!preg_match('/iphone/i',$ua)&&!preg_match('/android/i',$ua)&&!preg_match('/blackberry/i',$ua)&&!preg_match('/symb/i',$ua)&&!preg_match('/ipad/i',$ua)&&!preg_match('/ipod/i',$ua)&&!preg_match('/phone/i',$ua)){
				?>
		<!-- SMARTPHONE -->
			<link rel="stylesheet" type="text/css" href="css/_nosmart.css" />
			
		<!-- SMARTPHONE (fin) -->
				<?php
				//<script type="text/javascript" src="./01_js/_nosmart.js"></script>
			}
		?>
		
		<script type="text/javascript" src="js/_f.js"></script>
		<script type="text/javascript" src="js/_msg.js"></script>
		<script type="text/javascript" src="js/_diaporama.js"></script>
		<script type="text/javascript" src="js/_responsive.js"></script>
		<script type="text/javascript" src="js/_home.js"></script>
		<script type="text/javascript">
		var LARGEUR_MINIMUM = 1024;
		var LARGEUR_MINIMUM_MENU = 690;
		var TIMEOUT_RESIZE = false;
		var TIMEOUT_RESIZE_CONTENUS = false;
		var RAPPORT_IMAGE = 4/3;
		var PAGE_COURANTE = "<?php echo $PAGE_COURANTE; ?>";
		</script>
		<script type="text/javascript">
		function onload(){
			//1. On lance le diaporama.
			charge_diaporama();
			//2. On prépare les infobulles, etc.
			parcours_recursif();
			
			
			resize(true);
			filtre(true);
		}
		function filtre(laisser_ferme){
			if(typeof(laisser_ferme)=="undefined")
				laisser_ferme = false;
			if(laisser_ferme||element("filtre").className=="actif")
				element("filtre").className = "";
			else
				element("filtre").className = "actif";
		}
		
		
		function menu_site_smartphone(laisser_ferme){
			if(typeof(laisser_ferme)=="undefined")
				laisser_ferme = false;
			if(!dans_tab("ouvert",element("menuSmartphone_pages").className.split(" "))&&!laisser_ferme){
				menu_utilisateur_smartphone(true);
				filtre(laisser_ferme);
				element("menuSmartphone_pages").className = element("menuSmartphone_pages").className+" ouvert";
				element("menuSmartphone_pages").style.width = (largeur("menuSmartphone")-largeur("menuSmartphone_utilisateur"))+"px";
			}
			else{
				filtre(laisser_ferme);
				element("menuSmartphone_pages").className = element("menuSmartphone_pages").className.replace(" ouvert","");
				element("menuSmartphone_pages").style.width = (largeur("menuSmartphone")-largeur("menuSmartphone_utilisateur"))+"px";
				//element("menu").className = PAGE_COURANTE;
			}
		}
		
		function choix_page(el){
			PAGE_COURANTE = el.id.split("_")[1];
		}
		
		function menu_utilisateur_smartphone(laisser_ferme){
			if(typeof(laisser_ferme)=="undefined")
				laisser_ferme = false;
			if(!dans_tab("menu_utilisateur_ouvert",element("page").className.split(" "))&&!laisser_ferme){
				menu_site_smartphone(true);
				filtre(laisser_ferme);
				element("page").className += " menu_utilisateur_ouvert";
				element("menu_utilisateur").className += " ouvert";
				/*element("menu_utilisateur").className += " ouvert";
				element("contenu").className += " ouvert";*/
			}
			else{
				filtre(laisser_ferme);
				
				element("page").className = element("page").className.replace(" menu_utilisateur_ouvert","");
				element("menu_utilisateur").className = element("menu_utilisateur").className.replace(" ouvert","");
				/*
				element("menu_utilisateur").className = element("menu_utilisateur").className.replace(" ouvert","");
				element("contenu").className = element("contenu").className.replace(" ouvert");*/
			}
		}
		
		function filtre_click(){
			//On ferme le menu utilisateur ou le menu du site
			menu_utilisateur_smartphone(true);
			menu_site_smartphone(true);
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
			var zone = element("home_editorial_bloc");
			if(zone.className=="editorial_1"){
				zone.className = "editorial_2";
			}
			else if(zone.className=="editorial_2"){
				zone.className = "editorial_3";
			}
		}
		function diaporama_editorial_previous(){
			console.log("gauche");
			var zone = element("home_editorial_bloc");
			if(zone.className=="editorial_2"){
				zone.className = "editorial_1";
			}
			else if(zone.className=="editorial_3"){
				zone.className = "editorial_2";
			}
		}
		
		function parcours_recursif(depart,el){
			if(typeof(depart)=="undefined")
				depart = document.body;
			if(typeof(el)=="undefined")
				el = depart;
			//1. On regarde la class Name de l'Ã©lÃ©ment.
			prepare_element(el);
		
			if(el.firstChild){
				parcours_recursif(depart,el.firstChild);
			}
			else{ //On est au bout d'une branche, on vas commencer la remontÃ©.
				if(el.nextSibling){
					parcours_recursif(depart,el.nextSibling);
				}
				else{
					while(el!=depart&&!el.nextSibling){
						el = el.parentNode;
					}
					if(el!=depart) //on a trouvÃ© un nextSibling dans un parent
						parcours_recursif(depart,el.nextSibling);
					else //On a tout remontÃ© sans trouver de nouvel Ã©lÃ©ment.
						null;
				}
			}
		}

		function prepare_element(el){
			if(el.className!="undefined"&&el.className!=""){
				var reg_infobulle = /infobulle(\[.+\])?/gi;
				if(reg_infobulle.test(el.className)){ //L'Ã©lÃ©ment Ã  une infobulle.
					//var reg_infobulle = /infobulle(\[([\d\s\w'"\\]*)(|(haut|bas|gauche|droite|left|top|bottom|right))?\])?/gi;
					var reg_infobulle = /infobulle(\[(.+)\])?/gi;
					var valeurs = reg_infobulle.exec(el.className);
					var continuer = true;
					//On capture les paramÃ¨tres de l'infobulle (si le deuxiÃ¨me est manquant, on met par dÃ©faut Ã  droite).
					if(valeurs.length>2&&typeof(valeurs[2])!="undefined"&&valeurs[2]!=""){
						var contenu = valeurs[2].split("|");
						if(contenu.length>1){ //Tout est dans le crochet.
							var texte = contenu[0];
							var position = contenu[1];
						}
						else{	//On regarde le title
							if(typeof(el.title)!="undefined"&&el.title!=""){
								var texte = el.title;
								var position = contenu[0];
							}
							else{
								continuer = false;
							}
						}
					}
					else{
						if(typeof(el.title)!="undefined"&&el.title!=""){
							var texte = el.title;
							var position = "droite";
						}
						else{
							continuer = false;
						}
					}
					if(continuer){
						el.title = "";
						console.log(texte);
						el.contenu_infobulle = encodeURIComponent(texte);
						el.className = el.className.replace(/infobulle(\[(.+)\])?/gi,"");
						ajoute_evenement(el,"mouseover",'if(typeof(infobulle)=="function"){infobulle(this,"'+texte+'","'+encodeURIComponent(position)+'")}');
					}
				}
			}
		}
		
		function scrolling(parent){
			console.log("scroll");
			if(parent==element("page")){ //Scroll petit ecran
				console.log("scroll_petit");
				var scroll = element("page").scrollTop;
				if(!isNaN(parseInt(element("menu_utilisateur").style.top))) element("menu_utilisateur").style.top = null;
				if(!isNaN(parseInt(element("menu").style.top))) element("menu").style.top = null;
			}
			else{ //scroll ecran normal
				console.log("scroll_grand");
				var scroll = getScrollPosition()["y"];
				//On règle la position du menu de gauche, la position du menu d'en haut
				//MENU HAUT
				var defaut_top = haut("menu")-parseInt(getStyle(element("menu"),"top"));
				if(scroll>defaut_top){
					element("menu").style.top = scroll-defaut_top+"px";
					element("menu").style.boxShadow = "0px 0px 20px -10px rgba(0, 0, 0, 1)";
				}
				else{
					element("menu").style.top = null;
					element("menu").style.boxShadow = "none";
				}
				//SOUS MENU
				/*var hauteur_offset = haut("contenu");
				console.log("h : "+hauteur_offset)
				var hauteur_modif = (!isNaN(parseInt(element("barressmenu").style.top)))?parseInt(element("barressmenu").style.top):0;
				var scroll_menu = scroll+hauteur("menu")-hauteur("barressmenu");
				//var scroll_menu_descente = scroll_menu;
				
				if(scroll_menu>hauteur_offset){
					//Si descente
					if(hauteur_modif<scroll_menu-hauteur_offset)
						element("barressmenu").style.top = scroll_menu-hauteur_offset+"px";
					//Si montée
					else{
						scroll_menu += hauteur("barressmenu");
						element("barressmenu").style.top = scroll_menu-hauteur_offset+"px";
					}
				}
				else{
					element("barressmenu").style.top = 0+"px";
				}*/
				//MENU GAUCHE
				var defaut_top = 305;
				var scroll_menu = scroll+hauteur("menu")+21; //21 : espace entre le menu du haut et ce menu-ci
				if(scroll_menu>defaut_top){
					element("menu_utilisateur").style.top = scroll_menu+"px";
				}
				else{
					element("menu_utilisateur").style.top = null;
				}
			}
			
			//Enfin, on regle la position du filtre
			element("filtre").style.top = scroll+"px";
		}
		
		</script>
		<style type="text/css">
			.row{
				display: block;
				width: 100%;
			}
			.row.no_padding{
				padding-top: 0px;
			}
			
			.row .bloc{
				display: inline-block;
				text-align: justify;
				vertical-align: top;
			}
			.bloc h3{
				text-align: left;
				font-weight: normal;
				font-size: 1.5em;
				text-shadow: 1px 0px 0px rgba(0, 0, 0, 0.1), 0px 1px 0px rgba(255, 255, 255, 0.1);
				margin-bottom: 0.5em;
				margin-top: 1em;
			}
				h3.bleu{
					color: rgb(35, 170, 221);
				}
			.bloc.no_padding>div{
				padding: 0px;
				/*margin: 20px;*/
			}
			.bloc.padding_60>div{
				padding-left: 60px;
			}
			.bloc.padding_120>div{
				padding-left: 120px;
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
				max-width: 700px;
				min-width: 350px;
			}
			.bloc.moyen_petit{
				width: 33%;
				/*max-width: 683px;*/
				min-width: 200px;
			}
			
			.row.multi_row .bloc.grand, .row.multi_row .bloc.grand_moyen, .row.multi_row .bloc.moyen, .row.multi_row .bloc.moyen_petit{
				width: 100%;
				min-width: 0px;
			}
			
			
			
			.bloc.gris>div{
				border: 1px solid rgb(227, 214, 199);
				background-color: rgb(240, 237, 234);
				border-radius: 3px;
			}
			
			
			
			
			input[type="text"], textarea{
				border: 1px solid #E5E5E5;
				padding: 0.5em 1em;
				text-shadow: 0px 1px 0px #FFF;
				display: inline-block;
				text-align: left;
				font-weight: 300;
				font-size: 1em;
				font-family: "Source Sans Pro", "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
			}

			input[type="text"]:focus, textarea:focus{
				box-shadow: 0px 0px 10px rgba(87,96,60,0.2);
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
			
			
			
			/********
			AGENDA
			**/
			.bloc_gauche, .bloc_milieu, .bloc_droite{
				position: relative;
			}
			#zone_bloc_evenement_accueil.zone_grande .bloc_evenement_accueil.bloc_gauche>div{
				margin-right: 5px;
			}
			#zone_bloc_evenement_accueil.zone_grande .bloc_evenement_accueil.bloc_milieu>div{
				margin-left: 5px;
				margin-right: 5px;
			}
			#zone_bloc_evenement_accueil.zone_grande .bloc_evenement_accueil.bloc_droite>div{
				margin-left: 5px;
			}
			
			#zone_bloc_evenement_accueil.zone_moyenne .bloc_evenement_accueil.bloc_gauche>div{
				margin-right: 5px;
				margin-bottom: 5px;
			}
			#zone_bloc_evenement_accueil.zone_moyenne .bloc_evenement_accueil.bloc_milieu>div{
				margin-left: 5px;
				margin-bottom: 5px;
			}
			#zone_bloc_evenement_accueil.zone_moyenne .bloc_evenement_accueil.bloc_milieu+.bloc_evenement_accueil.bloc_milieu>div{
				margin-left: 0px;
				margin-right: 5px;
				margin-bottom: 0px;
				margin-top: 5px;
			}
			#zone_bloc_evenement_accueil.zone_moyenne .bloc_evenement_accueil.bloc_droite>div{
				margin-left: 5px;
				margin-top: 5px;
			}
			
			#zone_bloc_evenement_accueil.zone_petite .bloc_evenement_accueil>div{
				margin-bottom: 5px;
			}
			#zone_bloc_evenement_accueil.zone_petite .bloc_evenement_accueil+.bloc_evenement_accueil>div{
				margin-top: 5px;
			}
			#zone_bloc_evenement_accueil .bloc_milieu>div+div{
				margin-top: 10px;
			}
				
			#zone_bloc_evenement_accueil.zone_petite .bloc_gauche, #zone_bloc_evenement_accueil.zone_petite .bloc_milieu, #zone_bloc_evenement_accueil.zone_petite .bloc_droite, #zone_bloc_evenement_accueil.zone_petite .bloc_milieu{
				max-width: 500px;
				width: 100%;
			}
				
			#zone_bloc_evenement_accueil.zone_grande .bloc_gauche, #zone_bloc_evenement_accueil.zone_grande .bloc_droite{
				min-width: 320px;
				max-width: none;
				width: 40%;
			}
			#zone_bloc_evenement_accueil.zone_grande .bloc_milieu{
				min-width: 160px;
				max-width: none;
				width: 20%;
			}
				
				
			#zone_bloc_evenement_accueil.zone_moyenne .bloc_gauche, #zone_bloc_evenement_accueil.zone_moyenne .bloc_milieu, #zone_bloc_evenement_accueil.zone_moyenne .bloc_droite{
				min-width: 250px;
				max-width: 400px;
				width: 50%;
				/*display: inline-block;*/
			}
			
			
			#home_agenda_top3_bloc.zone_petite .bloc_gauche, #home_agenda_top3_bloc.zone_petite .bloc_milieu{
				width: 100%;
			}
			#home_agenda_top3_bloc.zone_petite .bloc_droite{
				width: 100%;
			}
			
			#home_agenda_top3_bloc.zone_moyenne .bloc_gauche, #home_agenda_top3_bloc.zone_moyenne .bloc_milieu{
				width: 50%;
			}
			#home_agenda_top3_bloc.zone_moyenne .bloc_droite{
				width: 100%;
			}
			
			#home_agenda_top3_bloc.zone_grande .bloc_gauche, #home_agenda_top3_bloc.zone_grande .bloc_milieu{
				width: 38%;
			}
			#home_agenda_top3_bloc.zone_grande .bloc_droite{
				width: 24%;
			}
			
			#home_agenda_top3_bloc .bloc_gauche>div, #home_agenda_top3_bloc .bloc_milieu>div{
				width: 50%;
				display: inline-block;
				vertical-align: middle;
			}
			
			#home_agenda_top3_bloc .bloc_gauche>div>div, #home_agenda_top3_bloc .bloc_milieu>div>div{
				margin-left: 10px;
			}
			
			#home_agenda_top3_bloc.zone_petite .bloc_gauche, #home_agenda_top3_bloc.zone_petite .bloc_milieu{
				margin-bottom: 10px;
			}
			
			#home_agenda_top3_bloc.zone_moyenne .bloc_droite{
				margin-top: 10px;
			}
			#home_agenda_top3_bloc.zone_grande .bloc_droite>div{
				margin-left: 10px;
			}
			#home_agenda_top3_bloc .bloc_evenement_accueil{
				vertical-align: middle;
			}
			
			#home_agenda_top3_bloc .blocTop3{
				font-size: 1.1em;
				color: rgb(255, 255, 255);
				padding-top: 5px;
				font-family: 'Yanone Kaffeesatz',sans-serif;
				font-weight: normal;
				text-transform: uppercase;
				letter-spacing: 0.15em;
			}
			#home_agenda_top3_bloc .blocTop3>span{
				font-size: 2.4em;
			}
			
			
			
			
			.bloc_evenement_accueil{
				display: inline-block;
				overflow: hidden;
				position: relative;
				text-align: center;
				vertical-align: top;
			}
			
			.bloc_evenement_accueil .image{
				border-radius: 5px;
				background-color: white;
				border: 1px solid rgb(138, 154, 162);
			}
			
			
			
			.image{
				position: relative;
				overflow: hidden;
			}
			.image>img{
				-webkit-transition: transform 500ms ease-out;
				-moz-transition: transform 500ms ease-out;
				-o-transition: transform 500ms ease-out;
				-ms-transition: transform 500ms ease-out;
				transition: transform 500ms ease-out;
			}
			.image:hover>img{
				-webkit-transform: scale(1.1,1.1);
				   -moz-transform: scale(1.1,1.1);
					-ms-transform: scale(1.1,1.1);
					 -o-transform: scale(1.1,1.1);
						transform: scale(1.1,1.1);
			}
			
			.bloc.logo_ei>div{
				background-image: url("img/fond_bloc_ei.png");
				background-repeat: no-repeat;
				background-position: center center;
			}
			
			
			
			.image:hover>.home_agenda_bloc_description{
				bottom: 0%;
			}
			.image:hover>.home_agenda_bloc_date{
				right: -100%;
			}
			
			.home_agenda_bloc_description{
				position: absolute;
				background-color: rgba(68, 81, 88, 0.8);
				bottom: -100%;
				color: white;
				width: 100%;
				text-align: left;
				max-height: 100%;
				
				-webkit-transition: bottom 500ms ease-out;
				-moz-transition: bottom 500ms ease-out;
				-o-transition: bottom 500ms ease-out;
				-ms-transition: bottom 500ms ease-out;
				transition: bottom 500ms ease-out;
			}
			
			.home_agenda_bloc_description>div{
				margin: 1em;
			}
			
			.home_agenda_bloc_date{
				position: absolute;
				/*height: 25px;*/
				overflow: hidden;
				background-color: rgb(233, 91, 42);
				color: white;
				bottom: 10px;
				right: 0px;
				opacity: 0.9;
				border-bottom-left-radius: 5px;
				border-top-left-radius: 5px;
				font-weight: bold;
				font-size: 1.2em;
				padding: 0.3em;
				text-align: right;
				
				-webkit-transition: right 500ms ease-out;
				-moz-transition: right 500ms ease-out;
				-o-transition: right 500ms ease-out;
				-ms-transition: right 500ms ease-out;
				transition: right 500ms ease-out;
			}/*
			.home_agenda_bloc_date.double{
				width: 133px;
			}*/
			.home_agenda_bloc_date div.jr{
				font-size: 1.7em;
				display: inline-block;
			}
			.home_agenda_bloc_date div.ms_an{
				display: inline-block;
				width: 28px;
				padding: 0px;
				text-transform: uppercase;
				font-size: 0.65em;
				line-height: 1.2em;
				text-align: center;
			}
		</style>
	</head>
	<body onload="onload()" onresize="resize()" onscroll="scrolling(this)">
		<?php
		echo '<div id="menuSmartphone">';
			echo '<div id="menuSmartphone_utilisateur" onclick="menu_utilisateur_smartphone()">';
				echo '<img src="http://www.sudplanete.net/_admin/africine_dev/03_interface/img_colorize.php?uri=ico_headerMenuSmartphone.png&c=b4b4b4" />';
			echo '</div>';
			echo '<div id="menuSmartphone_pages" onclick="menu_site_smartphone()">';
				echo '<div id="fleche_deplier"></div>';
				
				echo '<div id="menu" class="'.$PAGE_COURANTE.'">';
					echo '<a id="menu_home" class="item_menu gris" href="index.php?p=accueil" onclick="choix_page(this)"><div></div></a>';
					echo '<a id="menu_editorial" class="item_menu bleu" href="index.php?p=editorial" onclick="choix_page(this)"><div>&Eacute;ditorial</div></a>';
					echo '<a id="menu_agenda" class="item_menu rouge" href="index.php?p=forum" onclick="choix_page(this)"><div>Agenda</div></a>';
					echo '<a id="menu_petiteannonce" class="item_menu pomme" href="index.php?p=petiteannonce" onclick="choix_page(this)"><div>annonces</div></a>';
					echo '<a id="menu_repertoire" class="item_menu orange" href="index.php?p=structure" onclick="choix_page(this)"><div>R&eacute;pertoire</div></a>';
					echo '<a id="menu_forum" class="item_menu vert" href="index.php?p=editorial" onclick="choix_page(this)"><div>Forums</div></a>';
				echo '</div>';
				
			echo '</div>';
		echo '</div>';
		
		echo '<div id="menu_utilisateur">';
			echo '<div class="item_menu_utilisateur infobulle espace_personnel" title="Espace personnel"></div>';
			echo '<div class="item_menu_utilisateur infobulle ajouter_information" title="Ajouter une information"></div>';
			echo '<div class="item_menu_utilisateur infobulle animation" title="Animations"></div>';
			
			echo '<div class="item_menu_utilisateur infobulle vie_projet" title="Vie du projet"></div>';
			echo '<div class="item_menu_utilisateur infobulle guide_utilisation" title="Guide d\'utilisation"></div>';
			echo '<div class="item_menu_utilisateur infobulle page_facebook" title="Page facebook"></div>';
		echo '</div>';
		?>
		<div id="page" onscroll="scrolling(this)"><!-- <img width="693" height="183" alt="Ensemble ici" src="img/diapo-index/diapo-11.jpg" style="position: absolute; top: 0px; z-index: 5; opacity: 0; display: none;"></img> -->
			<div id="filtre" class="actif" onclick="filtre_click()"></div>
			<div id="header">
				<div id="logo"><img src="img/logo-ensembleici.png" alt="Ensemble ici" /></div>
				<div id="bandeau">
					<div id="diaporama"></div>
					<div id="cache"><img width="322" height="183" alt="Ensemble ici" src="img/bandeau-cache.png" /></div>
				</div>
				<div id="slogan"><img alt="Tous acteurs de la vie locale" src="img/bandeau-slogan.png" /></div>
			</div>
<?php
				
				echo '<div id="contenu">';
					echo '<div>';
						echo '<div id="barressmenu">';
							echo '<div>';
								echo '<h1 id="h1ville">Nyons</h1>';
								echo '<input type="button" class="ico map" value="Modifier" />';
							echo '</div>';
							echo '<div>';
								echo '<input id="recherche" class="recherche vide" type="text" name="recherche" value="Recherche" title="Recherche" maxlength="100" onfocus="input_focus(this);" onblur="input_blur(this);" />';
							echo '</div>';
						echo '</div>';
						echo '<div id="contenu_page">';
?>
