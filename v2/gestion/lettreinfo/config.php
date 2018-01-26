<?php
// chemin pour include racine admin
$doss = "../";
$pageGestion = true;
require_once('../../01_include/connexion_verif.php');
require_once('../../01_include/_connect.php');
require_once('../../01_include/_var_ensemble.php');

$titrecateg = "Lettres d'informations";
$pagemenuadm = "let";
$rep = "lettreinfo/";
// $chemin_img = "02_medias/05_evenement/";
// $chemin_img_mini = "02_medias/06_mini/";

// Libellés
$cc_maj = "Lettre(s) d'informations";
$cc_min = "lettre(s) d'informations";
$cc_act = "Utilisateur activé";
$cc_desact = "Utilisateur désactivé";
$cc_ajout = "Utilisateur ajouté";
$cc_modif = "Utilisateur modifié";
$cc_supp = "Utilisateur supprimé";
$cc_de = "de la lettre d'information";
$cc_cette = "Cette lettre d'information";
$cc_cettemin = "cette lettre d'information";
$cc_une = "une lettre d'information";
$cc_auc_nom = "Aucun nom d'utilisateur saisi";
$cc_auc_id = "Aucun id d'utilisateur défini";

$no_stage = 22;
$no_cours = 24;
$no_atelier = 1;

$les_tables = array("lettreinfo_edito"=>array("no_lettre","corps","etape_valide"));

?>
