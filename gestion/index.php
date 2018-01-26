<?php
session_name("EspacePerso2");
session_start();
require('/home/ensemble/01_include/_var_ensemble.php');
include "/home/ensemble/01_include/_fonctions.php";
//require('/media/www-dev/public/EnsembleIci/01_include/_var_ensemble.php');
//include "/media/www-dev/public/EnsembleIci/01_include/_fonctions.php";
/***
On récupère les droits
	1. les onglets accessibles
	2. les droits pour chaque onglets.
	3. 
**/
//if(!empty($_SESSION)){
$PAGE = ($_GET["page"]!="")?$_GET["page"]:"accueil";
setcookie('EI_dossier_img', $PAGE, time() + 3600 * 24, '/', null, false, true);
$NO = $_GET["no"];
$VILLE = $_GET["no_ville"];
$UTILISATEUR = $_GET["user"];
$TRI = $_GET["tri"];
$ORDRE = $_GET["ordre"];
$territoire = $_SESSION["utilisateur"]["territoire"];

if ($territoire != '') {
    $requete_territoire = "SELECT * FROM territoires WHERE id=:t";
    $tab_territoire = execute_requete($requete_territoire,array(":t"=>$territoire));
    $LIBELLE_TERRITOIRE = $tab_territoire[0]["nom"];
}

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
<!--[if IE 9]>    <html class="no-js lt-ie10" lang="fr"> <![endif]-->
<!--[if IE 10]>    <html class="no-js lt-ie11" lang="fr"> <![endif]-->
<!--[if gt IE 10]><!--> <html class="no-js" lang="fr"> <!--<![endif]-->
<head>
<!-- Permet de regler l'affichage correctement sur smartphone -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, height=device-height, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="content-language" content="fr" />

<title>Administration : <?php echo $LIBELLE_PAGE; ?></title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
<link rel="stylesheet" href="../css/_msg.css" type="text/css" />
<script src="../js/ckeditor/ckeditor.js"></script>

<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

<script type="text/javascript" src="../js/shortcut.js"></script>

<script type="text/javascript" src="../js/_f.js"></script>
<script type="text/javascript" src="../js/_msg.js"></script>
<script type="text/javascript" src="../js/_responsive.js"></script>
<script type="text/javascript" src="../js/admin.js"></script>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/admin_commun.js?t=<?= time() ?>"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="../js/jqueryUI/development-bundle/ui/i18n/jquery.ui.datepicker-fr.js"></script>

</head>
<!--<body id="body" onload="onload();">-->
<body id="body">
<section id="section_body"<?php echo ((!empty($_SESSION))?' class="connecte"':''); ?>>
	<?php
        
	if(!empty($_SESSION)){       
            if($PAGE=="editorial"||$PAGE=="evenement"||$PAGE=="structure"||$PAGE=="petite-annonce"||$PAGE=="forum"){
                    include "fiche_liste.php";
            }
            else if(is_file($PAGE.".php")){
                    include($PAGE.".php");
            }
            else
                    include("404.php");
	}
	else
            include("deconnecte.php");
	echo '<header id="header">';
		echo '<section id="fenetre_connexion"><form action="" method="post" onsubmit="return connexion();"><input type="text" id="input_email" /><br /><input type="password" id="input_mdp" /><br /><input type="submit" class="bleu" style="float:right;" value="Connexion" id="input_connexion" /><input type="button" style="float:left;" value="Mot de passe oublié" id="input_mdp_oublie" /></form><br /><div class="demander_espace"><div>&nbsp;</div><span class="lien">Comment obtenir mon espace éditeur ?</span></div></section>';
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
//        print_r($parametres); echo '*************';
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
</section><div id="filtre_chargement"><img src="../img/logo_ei_simple.png" /></div><div id="filtre_colorbox" onclick="filtre_click();"></div>
<?php include_once 'struct_modal.php'; ?>
<script src="../js/plupload/plupload.js"></script>
<script src="../js/plupload/plupload.flash.js"></script>
<script src="../js/plupload/plupload.html5.js"></script>
<?php if($PAGE=="editorial"||$PAGE=="evenement"||$PAGE=="structure"||$PAGE=="petite-annonce"||$PAGE=="forum"){ ?>
<script src="../js/mainplupload.js"></script>
<?php } ?>
<?php if ($PAGE=="publicites") { ?>
<script src="../js/mainplupload2.js"></script>
<script src="../js/mainplupload2bis.js"></script>
<script src="../js/mainplupload2ter.js"></script>
<?php } ?>
<?php if ($PAGE=="lettre-information") { ?>
<script src="../js/mainplupload3.js"></script>
<?php } ?>
<?php if ($PAGE=="territoire") { ?>
<script src="../js/mainplupload4.js"></script>
<?php } ?>
<script src="../js/tinymce/tinymce.min.js"></script>
    <script>
    tinymce.init({
        selector: "#contenu_bloc, #BDDchapo, #BDDdescription, #BDDlegende, #BDDnotes, #BDDdescription_complementaire, #BDDcontenu, #li_inp_edito, #li_inp_mention",
        theme: "modern",
        max_width: 900,
        height: 200,
        plugins: [
             "advlist autolink link image imagetools lists charmap print preview hr anchor pagebreak spellchecker",
             "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
             "save table contextmenu directionality emoticons template paste textcolor"
       ],
       toolbar: "undo redo | styleselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons", 
       style_formats: [
            {title: 'Violet foncé', inline: 'span', styles: {color: '#31287c', font : 'Helvetica light,Helvetica,Arial,Verdana,sans-serif'}},
            {title: 'Violet clair', inline: 'span', styles: {color: '#68398e', font : 'Helvetica light,Helvetica,Arial,Verdana,sans-serif'}},
            {title: 'Gras', inline: 'b'},
            {title: 'Normal', inline: 'span', styles: {color: '#000', font : 'Helvetica light,Helvetica,Arial,Verdana,sans-serif'}}
        ], 
        fontsize_formats: '10pt 12pt 14pt 16pt 18pt 24pt 36pt',        
        language : "fr_FR", 
        image_advtab: true,
        file_browser_callback : function(field_name, url, type, win) {
            tinymce.activeEditor.windowManager.open({
                url: $('#base_url').html() + '/medias/admin_index',
                title : 'Gallerie',
                width: 800,
                height: 400,
                resizable : 'yes',
                inline : 'yes',
                close_previous : 'no'
             }, {
                window : win,
                input : field_name
             }, {
                setUrl: function (url) {
                    win.document.getElementById(field_name).value = url;
                }
             }
            );
        }
     }); 
</script>
</body>
</html>
