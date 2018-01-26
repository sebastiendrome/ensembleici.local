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
    $date_debut = time();
    $num_jour_courant = date("N");
    $nb_jour_dimanche = 7-$num_jour_courant;
    //On calcul le timestamp du dimanche qui arrive
    $date_fin = $date_debut + ($nb_jour_dimanche+7)*24*60*60;
    $date_debut_lettre = date("Y-m-d");
    $date_fin_lettre = date("Y-m-d",$date_fin);
    $requete_liste_agenda = "SELECT E.no FROM evenement E, communautecommune_ville T, communautecommune C, villes V, genre G WHERE E.etat=1 AND E.validation = 1 AND E.titre NOT LIKE '%hebdomadaire%' AND E.no_genre<>24 AND E.apparition_lettre<2 AND E.date_debut<=:d_f AND E.date_fin >= :d_d AND V.id = E.no_ville AND T.no_ville = E.no_ville AND T.no_communautecommune = C.no AND C.territoires_id = :t AND E.no_genre = G.no";
    $res_liste_agenda = $connexion->prepare($requete_liste_agenda);
    $res_liste_agenda->execute(array(":d_d"=>$date_debut_lettre,":d_f"=>$date_fin_lettre, ":t" => $_SESSION["utilisateur"]["territoire"]));
    $tab_liste_agenda = $res_liste_agenda->fetchAll();
    
    $liste_evt = ''; $prem = 1;
    foreach ($tab_liste_agenda as $k => $v) {
        if ($prem == 1) {
            $prem = 0;
        }
        else {
            $liste_evt .= ',';
        }
        $liste_evt .= $v['no'];
    }
    
    $requete_liste = "SELECT E.no, E.titre, E.url_image, E.date_debut, V.nom_ville, V.nom_ville_url, E.no_ville, G.libelle, E.apparition_lettre FROM evenement E, genre G, villes V LEFT JOIN communautecommune_ville T ON T.no_ville = V.id LEFT JOIN communautecommune C ON T.no_communautecommune = C.no WHERE E.titre NOT LIKE '%hebdomadaire%' AND E.validation = 1 AND E.date_debut <= :d_f AND E.date_fin >= :d_d AND E.no NOT IN (".$liste_evt.") AND V.id = E.no_ville AND E.no_genre = G.no";
    $res_liste = $connexion->prepare($requete_liste);
    $res_liste->execute(array(":d_d"=>$date_debut_lettre,":d_f"=>$date_fin_lettre));
    $tab_liste = $res_liste->fetchAll();
}
else {
    $return_code = '10';
}

foreach ($tab_liste as $k => $v) {
    $tab[$k]['no']          = $v['no']; 
    $tab[$k]['titre']       = substr($v['titre'], 0, 20);
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
    $tab[$k]['date_debut']  = substr($v['date_debut'], 8, 2).'/'.substr($v['date_debut'], 5, 2).'/'.substr($v['date_debut'], 0, 4);
    $tab[$k]['nom_ville']   = $v['nom_ville'];
    $tab[$k]['lien']        = $root_site."evenement.".$v["nom_ville_url"].".".url_rewrite($v["titre"]).".".$v["no_ville"].".".$v["no"].".html";
    $tab[$k]['libelle']     = $v['libelle'];
    $tab[$k]['apparition']  = $v['apparition_lettre'];
}
$reponse = json_encode($tab); 
echo $reponse; 
?>
