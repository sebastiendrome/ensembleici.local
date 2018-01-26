<?php
// chemin pour include racine admin
$doss = "../";
$pageGestion = true;
require_once('../../01_include/connexion_verif.php');
require_once('../../01_include/_connect.php');
require_once('../../01_include/_var_ensemble.php');

$titrecateg = "Statuts de structures";
$pagemenuadm = "stt";
$rep = "statuts/";
// $chemin_img = "02_medias/05_evenement/";
// $chemin_img_mini = "02_medias/06_mini/";

// Libellés
$cc_maj = "Statut";
$cc_min = "statut";
$cc_act = "Statut activé";
$cc_desact = "Statut désactivé";
$cc_ajout = "Statut ajouté";
$cc_modif = "Statut modifié";
$cc_supp = "Statut supprimé";
$cc_de = "du statut";
$cc_cette = "Ce statut";
$cc_cettemin = "ce statut";
$cc_une = "un statut";
$cc_auc_nom = "Aucun nom de statut saisi";
$cc_auc_id = "Aucun id de statut défini";

?>