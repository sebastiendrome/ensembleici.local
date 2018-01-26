<?php
/***************************************************
Ce fichier contient Tout le haut de page:
	- La déclaration du document.
	- Le head html
	- La déclaration du body
		-Le filtre de la page
		-L'en-tête de la page
		-La declaration de "contenu"
		-La barre de sous menu
		-La declaration de "contenu_page"
		
Pour une optimisation du poids (pour les smartphones), le site est chargé de la manière suivante :
	1. On charge les CSS et JAVASCRIPT communs à toutes les versions, ainsi que les CSS prévues pour les petits écrans (fichier _css.css initialisé ainsi)
	2. Si l'utilisateur n'est ni sur tablette ni sur smartphone, on charge alors les CSS spéciales ordinateurs (boutons plus petits, etc.)
	3. En fonction de la taille de l'ecran, le javascript va gérer l'integration ou non du fichier _cssBig.css et la position du menu
	4. Une fois que tout est chargé, on retire alors du body la classe "chargement"
***/
if($PAGE_COURANTE=="editorial"||$PAGE_COURANTE=="agenda"||$PAGE_COURANTE=="petite-annonce"||$PAGE_COURANTE=="structure"||$PAGE_COURANTE=="forum") {
    $og_title = htmlspecialchars($tab_item["titre"]);
    if(!empty($tab_item["chapo"])) {
        $og_description = addslashes(strip_tags($tab_item["chapo"]));
    }
    else {
        $og_description = addslashes(strip_tags($tab_item["description"]));
    }
    if ($tab_item["image"] != '') {
        $og_image = $tab_item["image"];
    }
    else {
        $og_image = 'http://www.ensembleici.fr/img/logo2.png';
    }
}
else {
    $og_title="Ensemble ici : tous acteurs de la vie locale";
    $og_description = "Ensemble Ici est une initiative indépendante et citoyenne qui vise à faciliter les échanges et l’information de tous styles entre habitants et acteurs locaux. On sent bien aujourd’hui la nécessite de rompre avec l’individualisme ambiant, dans un monde qui se déshumanise. Internet nous offre la possibilité de développer des outils au service de tous : profitons-en! Nous pouvons ainsi mutualiser les informations, partager des services, donner de la visibilité aux initiatives locales, et mieux savoir ce qui anime notre région, tant au niveau culturel que social et citoyen.Une expérience participative qui peut être transposée en tous lieux !";
    $og_image = 'http://www.ensembleici.fr/img/logo2.png';
}

$facebook = 'https://www.facebook.com/ensembleici';
if (isset($_SESSION["utilisateur"]["facebook"])) {
    $facebook = $_SESSION["utilisateur"]["facebook"];
}
echo '<!doctype html>';
echo '<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="fr"><![endif]-->';
echo '<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" lang="fr"><![endif]-->';
echo '<!--[if IE 8]><html class="no-js lt-ie9" lang="fr"><![endif]-->';
echo '<!--[if gt IE 8]><!--><html class="no-js" lang="fr"><!--<![endif]-->';
	echo '<head>';
		echo '<title>Ensemble ici : tous acteurs de la vie locale</title>';
		//Cette ligne permet d'empêcher le zoom sur smartphone
		echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, height=device-height, maximum-scale=1.0" />';
		//Charset
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		//Langue
		echo '<meta http-equiv="content-language" content="fr" />';
                echo '<meta id="og_title" property="og:title" content="'.$og_title.'" />';
                echo "<meta id='og_description' property='og:description' content='".$og_description."' />";
                echo '<meta property="og:type" content="website" />';
                echo '<meta id="og_url" property="og:url" content="http://www.ensembleici.fr'.$_SERVER['REQUEST_URI'].'" />';
                echo '<meta id="og_image" property="og:image" content="'.$og_image.'" />';
		echo '<link type="text/css" href="http://fonts.googleapis.com/css?family=Handlee" rel="stylesheet" />';
		echo '<link type="text/css" rel="stylesheet" href="http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:400,700" />';
                echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">';
                echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">';
		//CSS
		echo '<link rel="stylesheet" type="text/css" href="css/_calendrier.css" />'; //Calendrier pour les inputs
                
//                echo '<link rel="stylesheet" type="text/css" href="js/jqueryUI/css/ui-lightness/jquery-ui-1.8.23.custom.css" />'; //Calendrier pour les inputs
                echo '<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">';
                
                
		echo '<link rel="stylesheet" href="css/_css.new.css" type="text/css" />'; //Fichier css de base (initialisé pour petits ecrans)
		echo '<link rel="stylesheet" href="css/_cssHome.css" type="text/css" />'; //Fichier css pour les blocs de la page d'accueil
		echo '<link rel="stylesheet" type="text/css" href="css/_msg.css" />'; //Fichier css pour les messages, infobulles, colorbox
                echo '<link rel="stylesheet" type="text/css" href="css/commun.css?t='.time().'" />';
                echo '<link rel="stylesheet" type="text/css" href="img/icofont/css/icofont.css" />';
                
                
		$ua = $_SERVER['HTTP_USER_AGENT'];
		if (!preg_match('/iphone/i',$ua)&&!preg_match('/android/i',$ua)&&!preg_match('/blackberry/i',$ua)&&!preg_match('/symb/i',$ua)&&!preg_match('/ipad/i',$ua)&&!preg_match('/ipod/i',$ua)&&!preg_match('/phone/i',$ua))
			echo '<link rel="stylesheet" type="text/css" href="css/_nosmart.css" />'; //Si ce n'est pas un smartphone ou une tablette (boutons plus gros, etc.)
		//JAVASCRIPT
		echo '<script type="text/javascript">';
			echo 'var LARGEUR_MINIMUM=1024;'; //Largeur de l'écran
			echo 'var LARGEUR_MINIMUM_MENU=690;'; //Largeur minimum du menu avant de passer en mode smartphone
			echo 'var TIMEOUT_RESIZE=false;'; //!=false si le script est en train de redimmensionner les blocs
			echo 'var TIMEOUT_RESIZE_CONTENUS=false;'; //!=false si le script est en train de redimmensionner les contenus des blocs
			echo 'var RAPPORT_IMAGE=4/3;'; //Rapport par défaut des images des fiches et listes du site
			echo 'var PAGE_COURANTE="'.$PAGE_COURANTE.'";'; //Page courante (accueil, editorial, evenement, etc.)
			echo 'var ID_VILLE='.$ID_VILLE.';'; //Numéro de la ville
			echo 'var NO='.$NO.';'; //Numéro de l'éventuelle fiche (si ouvert, sinon 0)
			echo 'var ROOT_PROD = "'.$root_site_prod.'";';
			echo 'var ROOT_SITE = "'.$root_site.'";';
		echo '</script>';
		echo '<script type="text/javascript" src="js/sc.js"></script>'; //Sound cloud
		echo '<script type="text/javascript" src="js/ckeditor/ckeditor.js"></script>'; //CK editor
		echo '<script type="text/javascript" src="js/_f.js"></script>'; //Bibliothèque développée en interne.
		echo '<script type="text/javascript" src="js/_msg.js"></script>'; //message, infobulles et colorBox
		
		echo '<script type="text/javascript" src="js/_responsive.js"></script>'; //Script permettant de recalculer certaines dimmensions
		echo '<script type="text/javascript" src="js/_home.js"></script>'; //Scripts utilisés pour la page d'accueil
		echo '<script type="text/javascript" src="js/_js.js?t='.time().'"></script>'; //Javascript principal de la page
		echo '<script type="text/javascript" src="js/_calendrier.js"></script>'; //Calendrier pour les inputs date

                echo '<script type="text/javascript" src="js/jquery.js"></script>'; //Jquery
                echo '<script type="text/javascript" src="js/_diaporama.js"></script>'; //Diaporama
                echo '<script type="text/javascript" src="js/commun.js?t='.time().'"></script>'; //Jquery
                echo '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>';
                
                echo '<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>';
                echo '<script src="js/jqueryUI/development-bundle/ui/i18n/jquery.ui.datepicker-fr.js"></script>';
                
		preg_match('/MSIE ([0-9]{1,}[\.0-9]{0,})/', $ua, $matches);
		$version = (float)$matches[1];
		if(!empty($version)&&$version<=9.0){
			echo '<style type="text/css">#filtre{display:none;}#filtre.actif{display:block;}</style>';
			if($version<=8.0){ //getELementsByClassName ... et oui les dinausores de microsoft ne se sont malheureusement pas tous éteind.
				echo '<script type="text/javascript" src="js/_dinausore.js"></script>'; //Calendrier pour les inputs date
			}
		}
		
		echo '<meta http-equiv="cache-control" content="no-cache" /><meta http-equiv="expires" content="0" /><meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" /><meta http-equiv="pragma" content="no-cache" />';
		
	echo '</head>';
	//Le body
	echo '<body class="chargement" onload="onload()" onresize="resize()" onscroll="scrolling()">'; 
		//Nouvelle réglementation CNIL (voir initialisation début de "_init_var.php")
		if(empty($_COOKIE["cookie_accepte"])){
			echo '<div id="information_cookies">';
				echo '<div><p>';
					echo 'En poursuivant votre navigation sur ensembleici.fr, vous acceptez l’utilisation de Cookies. Ces derniers permettent d\'enregistrer votre ville préférée afin de faciliter votre prochaine connexion.<a href="les-cookies.html" style="margin-left:2em;">En savoir plus.</a><img src="img/img_colorize.php?uri=non_actif.png&c=ffffff" onclick="accepter_cookies()" />';
				echo '</p></div>';
			echo '</div>';
		}
		// La barre de menu pour smartphone
		echo '<div id="menuSmartphone">';
			echo '<div id="menuSmartphone_utilisateur" onclick="menu_utilisateur_smartphone()">';
				echo '<img src="http://www.sudplanete.net/_admin/africine_dev/03_interface/img_colorize.php?uri=ico_headerMenuSmartphone.png&c=b4b4b4" />';
			echo '</div>';
			echo '<div id="menuSmartphone_pages" onclick="menu_site_smartphone()">';
				echo '<div id="fleche_deplier"></div>';
				
				echo '<div id="menu" class="'.$PAGE_COURANTE.'">';
					echo '<a id="menu_home" class="item_menu gris" href="'.$NOM_VILLE_URL.'.'.$ID_VILLE.'.html"><div></div></a>';
					echo '<a id="menu_editorial" class="item_menu bleu" href="'.$NOM_VILLE_URL.'.'.$ID_VILLE.'.editorial.30km.html"><div>Médias</div></a>';
					echo '<a id="menu_agenda" class="item_menu rouge" href="'.$NOM_VILLE_URL.'.'.$ID_VILLE.'.agenda.html" onclick="choix_page(this,event)"><div>Agenda</div></a>';
					echo '<a id="menu_petiteannonce" class="item_menu pomme" href="'.$NOM_VILLE_URL.'.'.$ID_VILLE.'.petite-annonce.30km.html" onclick="choix_page(this,event)"><div>annonces</div></a>';
					echo '<a id="menu_repertoire" class="item_menu orange" href="'.$NOM_VILLE_URL.'.'.$ID_VILLE.'.structure.html" onclick="choix_page(this,event)"><div>R&eacute;pertoire</div></a>';
					echo '<a id="menu_forum" class="item_menu vert" href="'.$NOM_VILLE_URL.'.'.$ID_VILLE.'.forum.30km.html" onclick="choix_page(this,event)"><div>Forums</div></a>';
				echo '</div>';
				
			echo '</div>';
		echo '</div>';
		// Le menu utilisateur
		echo '<div id="menu_utilisateur">';
			echo '<a href="espace-personnel.html" class="item_menu_utilisateur infobulle espace_personnel" title="Espace personnel"></a>';
			echo '<a href="espace-personnel.mes-fiches.html" class="item_menu_utilisateur infobulle ajouter_information" title="Ajouter une information"></a>';
                        echo '<a href="faire-un-don.html" class="item_menu_utilisateur infobulle lettres_informations" title="Faire un don"><img src="img/ico_cochon.png" style="margin-left:20px; margin-top:-25px; background-color:#e6e6e6;" /></a>';
//			echo '<a href="archives-lettres-informations.html" class="item_menu_utilisateur infobulle lettres_informations" title="lettres d\'informations"></a>';
			
			echo '<a href="vie-du-projet.html" class="item_menu_utilisateur infobulle vie_projet" title="Vie du projet"><img src="img/ico_logo.jpg" style="margin-left:20px; margin-top:-25px; background-color:#e6e6e6;" /></a>';
			echo '<a href="guide-utilisation.html" class="item_menu_utilisateur infobulle guide_utilisation" title="Guide d\'utilisation"></a>';
			echo '<a href="'.$facebook.'" class="item_menu_utilisateur infobulle page_facebook" id="lien_facebook" title="Page facebook"></a>';
		echo '</div>';
		//Filtre de chargement
		echo '<div id="barre_chargement"></div>';
		//La page
		echo '<div id="page" onscroll="scrolling()">';
			//Filtre (pour les colorBox, etc.)
			echo '<div id="filtre" class="actif" onclick="filtre_click()"></div>';
			//Header
			echo '<div id="header">';
				echo '<div id="logo"><img src="img/logo-ensembleici.png" alt="Ensemble ici" /></div>';
				echo '<div id="bandeau">';
					echo '<div id="diaporama"></div>';
					echo '<div id="cache"><img width="322" height="183" alt="Ensemble ici" src="img/bandeau-cache.png" /></div>';
				echo '</div>';
				echo '<div id="slogan"><img alt="Tous acteurs de la vie locale" src="img/bandeau-slogan.png" /></div>';
			echo '</div>';
			//Contenu
			echo '<div id="contenu" class="'.$PAGE_COURANTE.'">';
				echo '<div>';
					//Sous menu
					echo '<div id="barressmenu">';
                                            echo '<div>';
                                                echo '<h1 id="h1ville">'.((!empty($NOM_VILLE))?$NOM_VILLE:' ').'</h1>';
                                                echo '<input type="button" class="ico map" value="Modifier" onclick="fenetre_ville()" />';
                                                if(isset($_SESSION['id_connexion'])){
                                                echo '<input type="button" class="ico deconnexion" value="Déconnexion" onclick="deconnexion();" />';
                                                }
                                            echo '</div>';
                                                
						echo '<div>';
							echo '<img style="cursor:pointer;" id="img_open_newsletter" src="img/lettreinfo-inscription.png" onclick="fenetre_newsletter();" />';
						//echo '</div>';
						//echo '<div>';
							echo '<form onsubmit="return rechercher(this);" id="formulaire_recherche" action="recherche.php" method="get"><input id="q" class="recherche" type="text" name="q" title="Recherche" maxlength="100" /><input type="submit" /></form>';
						echo '</div>';
					echo '</div>';
					//Enfin le contenu de la page
					echo '<div id="contenu_page">';
?>
