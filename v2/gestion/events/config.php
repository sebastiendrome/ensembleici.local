<?php
// chemin pour include racine admin
$doss = "../";
$pageGestion = true;
require_once('../../01_include/connexion_verif.php');
require_once('../../01_include/_connect.php');
require_once('../../01_include/_var_ensemble.php');

$titrecateg = "Evènements";
$type_source = "evenement";
$pagemenuadm = "evt";
$rep = "events/";
$chemin_img = "02_medias/05_evenement/";
$chemin_img_mini = "02_medias/06_mini/";

// Libellés
$cc_act = "Evènement activé";
$cc_desact = "Evènement désactivé";
$cc_ajout = "Evènement ajouté";
$cc_modif = "Evènement modifié";
$cc_de = "de l'évènement";
$cc_cette = "Cet évènement";
$cc_cettemin = "cet évènement";
$cc_une = "un évènement";
$cc_auc_nom = "Aucun nom d'évènement saisi";
$cc_auc_id = "Aucun id d'évènement défini";

?>