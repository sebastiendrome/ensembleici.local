<?php
// chemin pour include racine admin
$doss = "../";
$pageGestion = true;
require_once('../../01_include/connexion_verif.php');
require_once('../../01_include/_connect.php');
require_once('../../01_include/_var_ensemble.php');

$titrecateg = "Contenu des blocs";
$pagemenuadm = "blc";
$rep = "blocs/";

// Libell�s
$cc_act = "Bloc activ�";
$cc_desact = "Bloc d�sactiv�";
$cc_ajout = "Bloc ajout�";
$cc_modif = "Bloc modifi�";
$cc_de = "du bloc";
$cc_cette = "Ce bloc";
$cc_cettemin = "ce bloc";
$cc_une = "un bloc";
$cc_auc_nom = "Aucun nom de bloc saisi";
$cc_auc_id = "Aucun id de bloc d�fini";

?>