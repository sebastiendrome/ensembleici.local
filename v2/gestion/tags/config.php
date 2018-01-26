<?php
// chemin pour include racine admin
$doss = "../";
$pageGestion = true;
require_once('../../01_include/connexion_verif.php');
require_once('../../01_include/_connect.php');
require_once('../../01_include/_var_ensemble.php');

$titrecateg = "Tags";
$pagemenuadm = "tag";
$rep = "tags/";
// $chemin_img = "02_medias/05_evenement/";
// $chemin_img_mini = "02_medias/06_mini/";

// Libellés
$cc_maj = "Tag";
$cc_act = "Tag activé";
$cc_desact = "Tag désactivé";
$cc_ajout = "Tag ajouté";
$cc_modif = "Tag modifié";
$cc_supp = "Tag supprimé";
$cc_de = "du tag";
$cc_cette = "Ce tag";
$cc_cettemin = "ce tag";
$cc_une = "un tag";
$cc_auc_nom = "Aucun nom de tag saisi";
$cc_auc_id = "Aucun id de tag défini";

?>