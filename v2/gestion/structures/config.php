<?php
$pageGestion = true;
require_once('../../01_include/connexion_verif.php');
require_once('../../01_include/_connect.php');
require_once('../../01_include/_var_ensemble.php');

// chemin pour include racine admin
$doss = "../";

$titrecateg = "Structures";
$type_source = "structure";
$pagemenuadm = "str";
$rep = "structures/";
$chemin_img = "02_medias/04_structure/";
$chemin_img_mini = "02_medias/06_mini/";

// Libellés
$cc_act = "Structure activée";
$cc_desact = "Structure désactivée";
$cc_ajout = "Structure ajoutée";
$cc_modif = "Structure modifiée";
$cc_de = "de la structure";
$cc_cette = "Cette structure";
$cc_cettemin = "cette structure";
$cc_une = "une structure";
$cc_auc_nom = "Aucun nom de structure saisi";
$cc_auc_id = "Aucun id de structure défini";

?>