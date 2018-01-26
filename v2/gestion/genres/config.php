<?php
// chemin pour include racine admin
$doss = "../";
$pageGestion = true;
require_once('../../01_include/connexion_verif.php');
require_once('../../01_include/_connect.php');
require_once('../../01_include/_var_ensemble.php');

$titrecateg = "Genres d'évènements";
$pagemenuadm = "gen";
$rep = "genres/";
// $chemin_img = "02_medias/05_evenement/";
// $chemin_img_mini = "02_medias/06_mini/";

// Libellés
$cc_maj = "Genre";
$cc_min = "genre";
$cc_act = "Genre activé";
$cc_desact = "Genre désactivé";
$cc_ajout = "Genre ajouté";
$cc_modif = "Genre modifié";
$cc_supp = "Genre supprimé";
$cc_de = "du genre";
$cc_cette = "Ce genre";
$cc_cettemin = "ce genre";
$cc_une = "un genre";
$cc_auc_nom = "Aucun nom de genre saisi";
$cc_auc_id = "Aucun id de genre défini";

?>