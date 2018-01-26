<?php
// chemin pour include racine admin
$doss = "../";
$pageGestion = true;
require_once('../../01_include/connexion_verif.php');
require_once('../../01_include/_connect.php');
require_once('../../01_include/_var_ensemble.php');

$titrecateg = "Lettres d'informations";
$pagemenuadm = "let";
$rep = "lettreinfo_users/";
$limite_par_page = 100;
// $chemin_img = "02_medias/05_evenement/";
// $chemin_img_mini = "02_medias/06_mini/";

// Libellés
$cc_maj = "Inscrit";
$cc_min = "inscrit";
$cc_act = "Inscrit activé";
$cc_desact = "Inscrit désactivé";
$cc_ajout = "Inscrit ajouté";
$cc_modif = "Inscrit modifié";
$cc_supp = "Inscrit supprimé";
$cc_de = "de l'inscrit";
$cc_cette = "Cet inscrit";
$cc_cettemin = "cet inscrit";
$cc_une = "un inscrit";
$cc_auc_nom = "Aucune adresse mail saisie";
$cc_auc_id = "Aucun id d'inscrit défini";

?>