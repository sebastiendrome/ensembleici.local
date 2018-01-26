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
//5. On charge maintenant le contenu de la page.
include "../01_include/_init_page.php";
//6. On retourne le json de la page pour que javascript l'insère
echo json_encode($lignes);

/*
include "../01_include/_var_ensemble.php";
include "../01_include/_fonctions.php";
//On récupère les paramètres
if(!isset($_POST["p"])||empty($_POST["p"]))
	$PAGE_COURANTE = "accueil";
else
	$PAGE_COURANTE = $_POST["p"];
$ID_VILLE = (!empty($_POST["id_ville"]))?$_POST["id_ville"]:0;
$NOM_VILLE = $_POST["nom_ville"];
$TITRE = $_POST["titre"];
$NO = (!empty($_POST["no"]))?$_POST["no"]:0;

//Paramètres supplémentaires
$NUMERO_PAGE = (!empty($_POST["np"]))?$_POST["np"]:1;
//$LIMITE = (!empty($_POST["limite"]))?$_POST["limite"]:10;

//Paramètres de filtres
//$DATE_DU = (!empty($_POST["du"]))?$_POST["du"]:"";

$DU = (!empty($_POST["du"]))?$_POST["du"]:((!empty($_GET["du"]))?$_GET["du"]:"");
$TRI = $_POST["tri"];
$DISTANCE = (!empty($_POST["dist"]))?$_POST["dist"]:((!empty($_GET["dist"]))?$_POST["dist"]:"");
	$DISTANCE = ($DISTANCE!="")?(($DISTANCE!="tous")?str_replace("km","",$DISTANCE):-1):0;


//On récupère les lignes
if($ID_VILLE>0){
	if($PAGE_COURANTE=="editorial"||$PAGE_COURANTE=="agenda"||$PAGE_COURANTE=="petite-annonce"||$PAGE_COURANTE=="structure"||$PAGE_COURANTE=="forum")
		include "../page.php";
	else{
		if($PAGE_COURANTE=="accueil")
			include "../accueil.php";
		else if($PAGE_COURANTE=="espace-perso")
			include "../espace_personel.php";
	}
}
else
	include "../accueil_sans_ville.php";
	*/


?>
