<?php
$contenu_droite = contenu_colonne_droite("autres_pages");
//On récupère le titre et le contenu du bloc "animation"
$territoire = 1;
if (isset($_SESSION["utilisateur"]["territoire"])) {
    $territoire = $_SESSION["utilisateur"]["territoire"];
}
$requete = "SELECT titre, contenu FROM contenu_blocs WHERE ref=14 AND etat=1 AND territoires_id = ".$territoire;
$tab_animation = execute_requete($requete);
$contenu = (count($tab_animation)>0)?$tab_animation[0]["contenu"]:"";

$tab_lettres_infos = execute_requete("SELECT lettreinfo.objet,lettreinfo.date_debut,lettreinfo.repertoire,lettreinfo_envoi.nb_envoi FROM lettreinfo JOIN lettreinfo_envoi ON lettreinfo.no_envoi=lettreinfo_envoi.no WHERE lettreinfo.territoires_id = $territoire ORDER BY lettreinfo.date_debut DESC");

$contenu .= '<div id="les_archives_lettre_info">';
for($i=0;$i<count($tab_lettres_infos);$i++){
	$contenu .= '<a class="une_archive_lettre_info" href="'.$tab_lettres_infos[$i]["repertoire"].'" target="_blank">';
		$contenu .= '<span class="date">'.date("d/m/Y",strtotime($tab_lettres_infos[$i]["date_debut"])).' :</span><span class="titre">'.$tab_lettres_infos[$i]["objet"].'</span>';
		$contenu .= '<div class="destinataires"">'.$tab_lettres_infos[$i]["nb_envoi"].' destinataires</div>';
	$contenu .= '</a>';
}
$contenu .= '</div>';

$ligne1 = array(array("class"=>"grand_moyen","id"=>"colonne_gauche","contenu"=>$contenu),array("class"=>"moyen_petit","id"=>"colonne_droite","contenu"=>$contenu_droite));
$lignes = array(array("lignes"=>$ligne1));
?>
