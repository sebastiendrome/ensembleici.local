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
$date_limite = date('Y-m-d', mktime(0, 0, 0, date("m")-1, date("d"),   date("Y")));

if (isset($_SESSION["utilisateur"]["territoire"])) {
    $date_debut_lettre = date("Y-m-d");          
    $requete_liste_annonces = "SELECT A.no, A.titre, A.url_image, V.nom_ville FROM petiteannonce A, communautecommune_ville T, communautecommune C, villes V WHERE A.etat=1 AND A.validation = 1 AND A.apparition_lettre<2 AND A.date_fin >= :d_d AND A.date_creation >= :d_l AND V.id = A.no_ville AND T.no_ville = A.no_ville AND T.no_communautecommune = C.no AND C.territoires_id = :t";
    $res_liste_annonces = $connexion->prepare($requete_liste_annonces);
    $res_liste_annonces->execute(array(":d_d"=>$date_debut_lettre, ":d_l"=>$date_limite, ":t" => $_SESSION["utilisateur"]["territoire"]));
    $tab_liste_annonces = $res_liste_annonces->fetchAll();
    
    $liste_ann = ''; $prem = 1;
    foreach ($tab_liste_annonces as $k => $v) {
        if ($prem == 1) {
            $prem = 0;
        }
        else {
            $liste_ann .= ',';
        }
        $liste_ann .= $v['no'];
    }
    
    if ($liste_ann == '') {
        $liste_ann = '0';
    }
    
    $requete_liste = "SELECT A.no, A.titre, A.url_image, V.nom_ville, V.nom_ville_url, A.no_ville, A.apparition_lettre FROM petiteannonce A, villes V LEFT JOIN communautecommune_ville T ON T.no_ville = V.id LEFT JOIN communautecommune C ON T.no_communautecommune = C.no WHERE A.etat=1 AND A.validation = 1 AND A.date_fin >= :d_d AND A.date_creation >= :d_l AND V.id = A.no_ville AND A.no NOT IN (".$liste_ann.")";
    $res_liste = $connexion->prepare($requete_liste);
    $res_liste->execute(array(":d_d"=>$date_debut_lettre, ":d_l"=>$date_limite));
    $tab_liste = $res_liste->fetchAll();
}
else {
    $return_code = '10';
}

foreach ($tab_liste as $k => $v) {
    $tab[$k]['no']          = $v['no']; 
    $tab[$k]['titre']       = $v['titre'];
    if ($v['url_image'] != '') {
        $tab[$k]['url_image']   = $root_site.$v['url_image'];
        $taille = getimagesize($root_serveur.$v['url_image']);
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
        $tab[$k]['url_image']   = '';
    }
    $tab[$k]['nom_ville']   = $v['nom_ville'];
    $tab[$k]['apparition']  = $v['apparition_lettre'];
    $tab[$k]['lien'] = $root_site."petiteannonce.".$v["nom_ville_url"].".".url_rewrite($v["titre"]).".".$v["no_ville"].".".$v["no"].".html";
}
$reponse = json_encode($tab); 
echo $reponse; 
?>
