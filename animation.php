<?php
$contenu_droite = contenu_colonne_droite("autres_pages");
//On récupère le titre et le contenu du bloc "animation"
$territoire = 1;
if (isset($_SESSION["utilisateur"]["territoire"])) {
    $territoire = $_SESSION["utilisateur"]["territoire"];
}
$requete = "SELECT titre, contenu FROM contenu_blocs WHERE ref=6 AND etat=1 AND territoires_id = ".$territoire;
$tab_animation = execute_requete($requete);
$contenu = (count($tab_animation)>0)?$tab_animation[0]["contenu"]:"";
$ligne1 = array(array("class"=>"grand_moyen","id"=>"colonne_gauche","contenu"=>$contenu),array("class"=>"moyen_petit","id"=>"colonne_droite","contenu"=>$contenu_droite));
$lignes = array(array("lignes"=>$ligne1));
?>
