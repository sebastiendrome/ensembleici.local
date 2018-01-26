<?php
session_start();
//1. Initialisation de la session
include "../../../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../../../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../../../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../../../01_include/_init_var.php";

$return_code = '0';
$tab = array();

if (isset($_SESSION["utilisateur"]["territoire"])) {
    $requete_liste = "SELECT S.no, S.nom, S.sous_titre, S.url_logo, V.nom_ville, F.libelle, S.apparition_lettre FROM structure S, statut F, villes V LEFT JOIN 
        communautecommune_ville T ON T.no_ville = V.id LEFT JOIN communautecommune C ON T.no_communautecommune = C.no WHERE S.etat=1 AND 
        V.id = S.no_ville AND F.no = S.no_statut AND S.nom LIKE '%".$_POST['key']."%' LIMIT 0,20";
    $res_liste = $connexion->prepare($requete_liste);
    $res_liste->execute();
    $tab_liste = $res_liste->fetchAll();
}
else {
    $return_code = '10';
}

foreach ($tab_liste as $k => $v) {
    $tab[$k]['no']          = $v['no']; 
    $tab[$k]['nom']         = $v['nom'];
    $tab[$k]['sous_titre']  = $v['sous_titre'];
    if ($v['url_logo'] != '') {
        $tab[$k]['url_logo']   = $root_site.$v['url_logo'];
        $taille = getimagesize($root_serveur.$v['url_logo']);
        $largeur = $taille[0];
        $hauteur = $taille[1];
        if ($largeur > $hauteur) {
            // marge en haut
            $ratio = $largeur / 140;
            $newhauteur = $hauteur / $ratio; 
            $margin_top = (140 - $newhauteur) / 2;
            $margin_left = 0;
        }
        else {
            // marge gauche
            $ratio = $hauteur / 140;
            $newlargeur = $largeur / $ratio; 
            $margin_left = (140 - $newlargeur) / 2;
            $margin_top = 0;
        }
        $tab[$k]['margin_left'] = $margin_left; 
        $tab[$k]['margin_top'] = $margin_top;
    }
    else {
        $tab[$k]['url_logo']   = '';
    }
    $tab[$k]['nom_ville']   = $v['nom_ville'];
    $tab[$k]['libelle']     = $v['libelle'];
    $tab[$k]['apparition']  = $v['apparition_lettre'];
}
$reponse = json_encode($tab); 
echo $reponse; 
?>
