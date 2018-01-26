<?php
header('Content-Type: text/plain; charset=UTF-8');
//1. Initialisation de la session
include "../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../01_include/_init_var.php";
//5. On traite la recherche
if(!empty($PAGE_COURANTE)&&($PAGE_COURANTE=="evenement"||$PAGE_COURANTE=="editorial"||$PAGE_COURANTE=="forum"||$PAGE_COURANTE=="structure"||$PAGE_COURANTE=="petite-annonce")){
	$reponse = array(true);
	if($PAGE_COURANTE=="evenement"){
		$nomTablePrincipale = "evenement";
		$nomChampTitre = "titre";
	}
	else if($PAGE_COURANTE=="editorial"){
		$nomTablePrincipale = "editorial";
		$nomChampTitre = "titre";
	}
	else if($PAGE_COURANTE=="structure"){
		$nomTablePrincipale = "structure";
		$nomChampTitre = "nom";
	}
	else if($PAGE_COURANTE=="forum"){
		$nomTablePrincipale = "forum";
		$nomChampTitre = "titre";
	}
	else{
		$nomTablePrincipale = "petiteannonce";
		$nomChampTitre = "titre";
	}
	$param = array("recherche"=>$_POST["q"],"distance"=>-1);
	$tab_recherche = extraire_liste($PAGE_COURANTE,30,1,$param);
}
else
	$tab_recherche = array();

echo json_encode(array("type"=>$PAGE_COURANTE,"resultat"=>$tab_recherche));
?>
