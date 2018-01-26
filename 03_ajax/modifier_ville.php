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
if(!empty($_POST["no"])){
	setcookie("id_ville", $no, time() + 365*24*3600,"/", null, false, true);
	$_SESSION["id_ville"] = $no;
        
        $requete = "SELECT T.facebook, T.facebook, T.url_don, C.territoires_id, T.code_ua FROM villes U LEFT JOIN communautecommune_ville V ON V.no_ville = U.id LEFT JOIN communautecommune C ON V.no_communautecommune = C.no LEFT JOIN territoires T ON C.territoires_id = T.id WHERE U.id = :no";
        $tab_requete = execute_requete($requete,array(":no"=>$_POST["no"]));
        if ($tab_requete[0]["territoires_id"] != '') {
            $_SESSION["utilisateur"]["facebook"] = $tab_requete[0]['facebook'];
            $_SESSION["utilisateur"]["territoire"] = $tab_requete[0]["territoires_id"];
            $_SESSION["utilisateur"]["code_ua"] = $tab_requete[0]["code_ua"];
            $_SESSION["utilisateur"]["url_don"] = $tab_requete[0]["url_don"];
            $_SESSION["utilisateur"]["url_adhesion"] = $tab_requete[0]["url_adhesion"];
        }
        else {
            $_SESSION["utilisateur"]["facebook"] = 'https://www.facebook.com/ensembleici';
            $_SESSION["utilisateur"]["territoire"] = 1;
            $_SESSION["utilisateur"]["code_ua"] = 'UA-32761608-1';
            $_SESSION["utilisateur"]["url_don"] = "https://www.helloasso.com/associations/association-decor/formulaires/2"; 
            $_SESSION["utilisateur"]["url_adhesion"] = "https://www.helloasso.com/associations/association-decor/adhesions/adhesion-a-l-association-decor";
        }
        echo $tab_requete[0]['facebook'];
}
?>
