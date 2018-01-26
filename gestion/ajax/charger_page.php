<?php
header('Content-type: text/plain; charset=utf-8');
//require_once('/home/ensemble/www/00_dev_sam/01_include/_var_ensemble.php');
//include_once("/home/ensemble/www/00_dev_sam/01_include/_fonctions.php");
require_once('/home/ensemble/01_include/_var_ensemble.php');
include_once("/home/ensemble/01_include/_fonctions.php");

//1. On vérifie la connexion
if(est_connecte()){
	$PAGE = $_POST["page"];
	$NO = $_POST["no"];
	$VILLE = $_POST["no_ville"];
	$UTILISATEUR = $_POST["user"];
	$TRI = $_POST["tri"];
	$ORDRE = $_POST["ordre"];
	$CP = $_POST["cp"];
	$PSEUDO = "sam";
	$FONCTION = "éditeur";
	
	if(a_droit($PAGE,false,false)){
		$requete_page = "SELECT * FROM administrationMenu WHERE url_rewrite=:p";
		$tab_page = execute_requete($requete_page,array(":p"=>$PAGE));
		$LIBELLE_PAGE = $tab_page[0]["libelle"];
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
		/*
		if(($PAGE=="accueil"||$PAGE=="editorial"||$PAGE=="evenement"||$PAGE=="structure"||$PAGE=="petite-annonce"||$PAGE=="forum"||$PAGE=="contact-administrateur")){
			if(is_file("../".$PAGE.".php"))
				include("../".$PAGE.".php");
			else
				include("../chantier.php");
		}
		else
			include("../404.php");*/
		if($PAGE=="editorial"||$PAGE=="evenement"||$PAGE=="structure"||$PAGE=="petite-annonce"||$PAGE=="forum"){
			include "../fiche_liste.php";
		}
		else if(is_file("../".$PAGE.".php")){
			include("../".$PAGE.".php");
		}
		else
			include("../404.php");
	
		$page["titre"] = html_entity_decode($LIBELLE_PAGE,ENT_QUOTES,"UTF-8");
		$page["ville"] = $LIBELLE_VILLE;
		$page["utilisateur"] = $NOM_UTILISATEUR;
		$page["cp"] = $CP;
		
		$return = array(true,$page);
	}
	else{
		$return = array(false,"[DROIT]");
	}
}
else{
	$return = array(false,"[CONNEXION]");
}

echo json_encode($return);
?>
