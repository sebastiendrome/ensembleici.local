<?php
// chemin pour include racine admin
$doss = "../";
$pageGestion = true;
require_once('../../01_include/connexion_verif.php');
require_once('../../01_include/_connect.php');
require_once('../../01_include/_var_ensemble.php');

$titrecateg = "Utilisateurs";
$pagemenuadm = "use";
$rep = "users/";
// $chemin_img = "02_medias/05_evenement/";
// $chemin_img_mini = "02_medias/06_mini/";

// Libellés
$cc_maj = "Utilisateur";
$cc_min = "utilisateur";
$cc_act = "Utilisateur activé";
$cc_desact = "Utilisateur désactivé";
$cc_ajout = "Utilisateur ajouté";
$cc_modif = "Utilisateur modifié";
$cc_supp = "Utilisateur supprimé";
$cc_de = "de l'utilisateur";
$cc_cette = "Cet utilisateur";
$cc_cettemin = "cet utilisateur";
$cc_une = "un utilisateur";
$cc_auc_nom = "Aucun nom d'utilisateur saisi";
$cc_auc_id = "Aucun id d'utilisateur défini";

?>