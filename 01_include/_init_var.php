<?php
/***
Ce fichier permet la gestion de id_ville
**/
//1. Pour faire les tests, on met l'éventuel $_GET dans le $_POST
/*if(empty($_POST)&&!empty($_GET)){
	$_POST = $_GET;
	unset($_GET);
}*/
if(!empty($_GET)){
	foreach($_GET as $cle=>$valeur){
		if(!isset($_POST[$cle])||empty($_POST[$cle]))
			$_POST[$cle] = $valeur;
	}
}
unset($_GET);
//2. $_GET n'a maintenant plus d'importance, on ne considère que $_POST, $_SESSION et $_COOKIE
$ID_VILLE = (!empty($_SESSION["id_ville"]))?$_SESSION["id_ville"]:((!empty($_COOKIE["id_ville"]))?$_COOKIE["id_ville"]:((!empty($_POST["id_ville"]))?$_POST["id_ville"]:0));
//3. Traitement si $ID_VILLE>0, dans le cas contraire aucun traitement nescessaire, de toute manière le contenu ne s'affichera pas, et javascript ouvrira la colorbox pour le choix de la ville ou de la connexion
if($ID_VILLE>0){
	//4. On met à jour éventuellement le cookie et la session
	if($_COOKIE["id_ville"]!=$ID_VILLE)
		setcookie("id_ville", $ID_VILLE, time() + 365*24*3600,"/", null, false, true);
	if($_SESSION["id_ville"]!=$ID_VILLE)
		$_SESSION["id_ville"]=$ID_VILLE;
	
	//5. Les variables suivantes ne sont remplies que dans le cas où $ID_VILLE est lui aussi remplie (cela signifie que l'on est sur l'accueil ou une fiche, ou une liste (edito,agenda,annonce,repertoire,forum)
	//On récupère le nom de la ville
//	$requete_ville = "SELECT nom_ville_maj FROM villes WHERE id=:id";
        $requete_ville = "SELECT nom_ville_maj, T.facebook, T.url_don, T.url_adhesion, C.territoires_id, T.code_ua FROM villes V LEFT JOIN communautecommune_ville A ON V.id = A.no_ville LEFT JOIN communautecommune C ON A.no_communautecommune = C.no LEFT JOIN territoires T ON T.id = C.territoires_id  WHERE V.id=:id";
	$infos_ville = execute_requete($requete_ville,array(":id"=>$ID_VILLE));
        if ($infos_ville[0]["territoires_id"] != '') {
            $_SESSION["utilisateur"]["facebook"] = $infos_ville[0]["facebook"];
            $_SESSION["utilisateur"]["territoire"] = $infos_ville[0]["territoires_id"];
            $_SESSION["utilisateur"]["code_ua"] = $infos_ville[0]["code_ua"];
            $_SESSION["utilisateur"]["url_don"] = $infos_ville[0]["url_don"];
            $_SESSION["utilisateur"]["url_adhesion"] = $infos_ville[0]["url_adhesion"];
        }
        else {
            $_SESSION["utilisateur"]["facebook"] = 'https://www.facebook.com/ensembleici';
            $_SESSION["utilisateur"]["territoire"] = 1;
            $_SESSION["utilisateur"]["code_ua"] = 'UA-32761608-1';
            $_SESSION["utilisateur"]["url_don"] = "https://www.helloasso.com/associations/association-decor/formulaires/2"; 
            $_SESSION["utilisateur"]["url_adhesion"] = "https://www.helloasso.com/associations/association-decor/adhesions/adhesion-a-l-association-decor";
        }
	$NOM_VILLE = $infos_ville[0]["nom_ville_maj"];
	$NOM_VILLE_URL = url_rewrite($NOM_VILLE); //Il existe aussi en principe dans la variable $_POST["nom_ville"].
	
	$PREVISUALISATION = !empty($_POST["previsualisation"]);
	
	if(!isset($_POST["p"])||empty($_POST["p"]))
		$PAGE_COURANTE = "accueil";
	else
		$PAGE_COURANTE = $_POST["p"];
		//Pour les fiches
	$TITRE = $_POST["titre"];
	$NO = (!empty($_POST["no"]))?$_POST["no"]:0;
		//Pour les listes
	$DISTANCE = (!empty($_POST["dist"]))?$_POST["dist"]:((!empty($_POST["dist"]))?$_POST["dist"]:"");
		$DISTANCE = ($DISTANCE!="")?(($DISTANCE!="tous")?str_replace("km","",$DISTANCE):-1):0;
	$TRI = $_POST["tri"];
	$NUMERO_PAGE = (!empty($_POST["np"]))?$_POST["np"]:1;
	$reg_date_url = "#([0-9]{2})-([0-9]{2})-([0-9]{4})#i";
	if(!empty($_POST["du"])&&preg_match($reg_date_url,$_POST["du"],$du)){
		$DU = $du[3]."-".$du[2]."-".$du[1];
		$DU_URL = $_POST["du"];
	}
	$reg_liste_tag = "#[0-9]+(-[0-9]+)*#i";
	if(!empty($_POST["tags"])&&preg_match($reg_liste_tag,$_POST["tags"])){
		$LISTE_TAGS = str_replace("-",",",$_POST["tags"]);
	}
}
else
	$NO = (!empty($_POST["no"]))?$_POST["no"]:0;
?>
