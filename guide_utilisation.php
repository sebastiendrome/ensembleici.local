<?php
$contenu_droite = contenu_colonne_droite("autres_pages");
//On récupère le titre et le contenu du bloc "animation"
$requete = "SELECT titre, contenu FROM contenu_blocs WHERE no=11 AND etat=1";
$tab_animation = execute_requete($requete);
$contenu = (count($tab_animation)>0)?$tab_animation[0]["contenu"]:"";
$ligne1 = array(array("class"=>"grand_moyen","id"=>"colonne_gauche","contenu"=>$contenu),array("class"=>"moyen_petit","id"=>"colonne_droite","contenu"=>$contenu_droite));
$lignes = array(array("lignes"=>$ligne1));
?>
