<?php
// chemin pour include racine admin
$doss = "../";
$pageGestion = true;
require_once('../../01_include/connexion_verif.php');
require_once('../../01_include/_connect.php');
require_once('../../01_include/_var_ensemble.php');

$titrecateg = "Sous-tags";
$pagemenuadm = "sst";
$rep = "sstags/";
// $chemin_img = "02_medias/05_evenement/";
// $chemin_img_mini = "02_medias/06_mini/";

// Libellés
$cc_maj = "Sous-tag";
$cc_act = "Sous-tag activé";
$cc_desact = "Sous-tag désactivé";
$cc_ajout = "Sous-tag ajouté";
$cc_modif = "Sous-tag modifié";
$cc_supp = "Sous-tag supprimé";
$cc_de = "du sous-tag";
$cc_cette = "Ce sous-tag";
$cc_cettemin = "ce sous-tag";
$cc_une = "un sous-tag";
$cc_auc_nom = "Aucun nom de sous-tag saisi";
$cc_auc_id = "Aucun id de sous-tag défini";

?>