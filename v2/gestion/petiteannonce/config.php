<?php
// chemin pour include racine admin
$doss = "../";
$pageGestion = true;
require_once('../../01_include/connexion_verif.php');
require_once('../../01_include/_connect.php');
require_once('../../01_include/_var_ensemble.php');

$titrecateg = "Petites annonces";
$type_source = "petiteannonce";
$pagemenuadm = "pea";
$rep = "petiteannonce/";
$chemin_img = "02_medias/09_petiteannonce/";
$chemin_img_mini = "02_medias/06_mini/";

// Libellés
$cc_act = "Petite annonce activée";
$cc_desact = "Petite annonce désactivée";
$cc_ajout = "Petite annonce ajoutée";
$cc_aucun = "Aucune petite annonce";
$cc_modif = "Petite annonce modifiée";
$cc_supp = "Petite annonce supprimée";
$cc_de = "de la petite annonce";
$cc_cette = "Cette petite annonce";
$cc_cettemin = "cette petite annonce";
$cc_une = "une petite annonce";
$cc_auc_nom = "Aucun nom de petite annonce saisi";
$cc_auc_id = "Aucun id de petite annonce défini";

?>