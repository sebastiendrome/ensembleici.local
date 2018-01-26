<?php
session_name("EspacePerso2");
session_start();
//require('/home/ensemble/www/00_dev_sam/01_include/_var_ensemble.php');
//include "/home/ensemble/www/00_dev_sam/01_include/_fonctions.php";
require('/home/ensemble/01_include/_var_ensemble.php');
include "/home/ensemble/01_include/_fonctions.php";
/***
On récupère les droits
	1. les onglets accessibles
	2. les droits pour chaque onglets.
	3. 
**/
//if(!empty($_SESSION)){

$PAGE = ($_GET["page"]!="")?$_GET["page"]:"accueil";
$NO = $_GET["no"];
$VILLE = $_GET["no_ville"];
$UTILISATEUR = $_GET["user"];
$TRI = $_GET["tri"];
$ORDRE = $_GET["ordre"];

if(!empty($VILLE)){
	//On récupère les infos de la ville
	$requete_ville = "SELECT nom_ville_maj FROM villes WHERE id=:v";
	$tab_ville = execute_requete($requete_ville,array(":v"=>$VILLE));
	$LIBELLE_VILLE = $tab_ville[0]["nom_ville_maj"];
}
if(!empty($UTILISATEUR)){
	$requete_utilisateur = "SELECT IF(utilisateur.pseudo='',utilisateur.email,utilisateur.pseudo) AS libelle FROM utilisateur WHERE utilisateur.no=:no";
	$tab_utilisateur = execute_requete($requete_utilisateur,array(":no"=>$UTILISATEUR));
	$NOM_UTILISATEUR = $tab_utilisateur[0]["libelle"];
}

//On récupère les infos de la page
$requete_page = "SELECT * FROM administrationMenu WHERE url_rewrite=:p";
$tab_page = execute_requete($requete_page,array(":p"=>$PAGE));
$LIBELLE_PAGE = $tab_page[0]["libelle"];

$requete_menu = "SELECT * FROM  administrationMenu JOIN droit_administrationMenu ON droit_administrationMenu.no_administrationMenu=administrationMenu.no WHERE droit_administrationMenu.no_droit=:no_droit";
$tab_menu = execute_requete($requete_menu,array(":no_droit"=>$_SESSION["droit"]["no"]));
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="fr"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="fr"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="fr"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="fr"> <!--<![endif]-->
<head>
<!-- Permet de regler l'affichage correctement sur smartphone -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, height=device-height, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="content-language" content="fr" />

<title>Administration : <?php echo $LIBELLE_PAGE; ?></title>

<link rel="stylesheet" href="../css/_msg.css" type="text/css" />
<script src="../js/ckeditor/ckeditor.js"></script>

<link rel="stylesheet" href="../css/admin.css" type="text/css" />

<script type="text/javascript" src="../js/shortcut.js"></script>

<script type="text/javascript" src="../js/_f.js"></script>
<script type="text/javascript" src="../js/_msg.js"></script>
<script type="text/javascript" src="../js/_responsive.js"></script>
<script type="text/javascript" src="../js/admin.js"></script>

</head>
<body id="body" onload="onload()">
<section id="section_body"<?php echo ((!empty($_SESSION))?' class="connecte"':''); ?>>
	<?php
	if(!empty($_SESSION)){
		//if(!empty($_GET["dev"])){
			if($PAGE=="editorial"||$PAGE=="evenement"||$PAGE=="structure"||$PAGE=="petite-annonce"||$PAGE=="forum"){
				include "fiche_liste.php";
			}
			else if(is_file($PAGE.".php")){
				include($PAGE.".php");
			}
			else
				include("404.php");
		//}
		/*else{
			if(($PAGE=="accueil"||$PAGE=="editorial"||$PAGE=="evenement"||$PAGE=="structure"||$PAGE=="petite-annonce"||$PAGE=="forum"||$PAGE=="contact-administrateur")){
				if(is_file($PAGE.".php"))
					include($PAGE.".php");
				else
					include("chantier.php");
			}
			else
				include("404.php");
		}*/
	}
	else
		include("deconnecte.php");
	echo '<header id="header">';
		echo '<section id="fenetre_connexion"><input type="text" id="input_email" /><br /><input type="password" id="input_mdp" /><br /><input type="button" class="bleu" style="float:right;" value="Connexion" onclick="connexion()" id="input_connexion" /><input type="button" style="float:left;" value="Mot de passe oublié" id="input_mdp_oublie" /><br /><div class="demander_espace"><div>&nbsp;</div><span class="lien">Comment obtenir mon espace éditeur ?</span></div></section>';
		echo '<section id="barre_menu">';
			echo '<div id="bouton_menu_principal" onmouseover="ouvrir_menu(\'principal\');">'.$LIBELLE_PAGE.'</div>';
			echo '<div id="bouton_menu_personnel" onmouseover="ouvrir_menu(\'personnel\');">'.((!empty($_SESSION))?'<span id="span_pseudo">'.$_SESSION["utilisateur"]["pseudo"].'</span> : '.$_SESSION["droit"]["libelle"]:"...").'</div>';
			echo '<nav id="principal">';
				for($i=0;$i<count($tab_menu);$i++){
					echo '<a href="?page='.$tab_menu[$i]["url_rewrite"].'&no=" id="page_'.$tab_menu[$i]["url_rewrite"].'" class="item_menu '.$tab_menu[$i]["url_rewrite"].'">'.$tab_menu[$i]["libelle"].'</a>';
				}
			echo '</nav>';
			echo '<nav id="personnel">';
				echo '<div class="item_menu infoperso" onclick="fenetre_pseudo()">Modifier mes informations personnelles</div>';
				echo '<div class="item_menu mdp">Modifier mon mot de passe</div>';
				echo '<div class="item_menu deconnexion" onclick="deconnexion()">D&eacute;connexion</div>';
			echo '</nav>';
		echo '</section>';
	echo '</header>';
	echo '<section id="contenu" class="'.$PAGE.' '.(($page["menu"]!=false)?"avec_menu":"sans_menu").'">';
		echo '<nav id="contenu_menu">';
			if($page["menu"]!=false)
				echo $page["menu"];
		echo '</nav>';
		echo '<section id="contenu_contenu">';
			echo $page["contenu"];
		echo '</section>';
	echo '</section>';
	

?>
</section><div id="filtre_chargement"><img src="../img/logo_ei_simple.png" /></div><div id="filtre_colorbox" onclick="filtre_click()"></div>
</body>
</html>
