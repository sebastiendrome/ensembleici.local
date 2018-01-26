<?php
$affmenu_adm = '<ul id="ad-menuhaut">';

if ($connexion_admin_droits == "A") 
{
	$page_accueil = "accueil_admin.php";
	$droits_est_admin = true;
}
elseif ($connexion_admin_droits == "E")
{
	$page_accueil = "accueil_editeur.php";
	$droits_est_editeur = true;
}

/* Accueil */
  if ( $pagemenuadm=="acc" ) { $affmenu_adm .= '<li class="actif">'; } else { $affmenu_adm .= '<li>'; }
  $affmenu_adm .= '<a href="'.$doss.$page_accueil.'" title="Accueil"><span class="ui-icon ui-icon-carat-1-e"></span> Accueil administration</a></li>';

if ( $pagemenuadm=="evt" ) { $affmenu_adm .= '<li class="actif">'; } else { $affmenu_adm .= '<li>'; }
$affmenu_adm .= '<a href="'.$doss.'events/admin.php" title="Gestion des évènements"><span class="ui-icon ui-icon-calendar"></span> Evènements</a></li>';

if ( $pagemenuadm=="str" ) { $affmenu_adm .= '<li class="actif">'; } else { $affmenu_adm .= '<li>'; }
$affmenu_adm .= '<a href="'.$doss.'structures/admin.php" title="Gestion des structures"><span class="ui-icon ui-icon-home"></span> Structures</a></li>';

if ( $pagemenuadm=="pea" ) { $affmenu_adm .= '<li class="actif">'; } else { $affmenu_adm .= '<li>'; }
$affmenu_adm .= '<a href="'.$doss.'petiteannonce/admin.php" title="Gestion des petites annonces"><span class="ui-icon ui-icon-pin-s"></span> Petites annonces</a></li>';

// Affichage pour les admin uniquement (pas pour les éditeurs)
if ($droits_est_admin)
{
	if ( $pagemenuadm=="tag" ) { $affmenu_adm .= '<li class="actif">'; } else { $affmenu_adm .= '<li>'; }
$affmenu_adm .= '<a href="'.$doss.'tags/admin.php" title="Gestion des tags"><span class="ui-icon ui-icon-tag"></span> Tags</a></li>';

	if ( $pagemenuadm=="sst" ) { $affmenu_adm .= '<li class="actif">'; } else { $affmenu_adm .= '<li>'; }
	$affmenu_adm .= '<a href="'.$doss.'sstags/admin.php" title="Gestion des sous-tags"><span class="ui-icon ui-icon-tag"></span> Sous-tags</a></li>';

	if ( $pagemenuadm=="blc" ) { $affmenu_adm .= '<li class="actif">'; } else { $affmenu_adm .= '<li>'; }
	$affmenu_adm .= '<a href="'.$doss.'blocs/admin.php" title="Gestion du contenu des pages"><span class="ui-icon ui-icon-pencil"></span> Contenus des blocs</a></li>';

	if ( $pagemenuadm=="use" ) { $affmenu_adm .= '<li class="actif">'; } else { $affmenu_adm .= '<li>'; }
	$affmenu_adm .= '<a href="'.$doss.'users/admin.php" title="Gestion des utilisateurs"><span class="ui-icon ui-icon-person"></span> Utilisateurs</a></li>';

	if ( $pagemenuadm=="let" ) { $affmenu_adm .= '<li class="actif">'; } else { $affmenu_adm .= '<li>'; }
	$affmenu_adm .= '<a href="'.$doss.'lettreinfo/admin.php" title="Gestion des lettres d\'informations"><span class="ui-icon ui-icon-mail-closed"></span> Lettres d\'informations</a></li>';

	if ( $pagemenuadm=="gen" ) { $affmenu_adm .= '<li class="actif">'; } else { $affmenu_adm .= '<li>'; }
	$affmenu_adm .= '<a href="'.$doss.'genres/admin.php" title="Gestion des genres d\'évènements"><span class="ui-icon ui-icon-contact"></span>Genres évènements</a></li>';

	if ( $pagemenuadm=="stt" ) { $affmenu_adm .= '<li class="actif">'; } else { $affmenu_adm .= '<li>'; }
	$affmenu_adm .= '<a href="'.$doss.'statuts/admin.php" title="Gestion des statuts des structures"><span class="ui-icon ui-icon-contact"></span> Statuts structures</a></li>';

	if ( $pagemenuadm=="imp" ) { $affmenu_adm .= '<li class="actif">'; } else { $affmenu_adm .= '<li>'; }
	$affmenu_adm .= '<a href="'.$doss.'import/admin.php" title="Gestion des importations"><span class="ui-icon ui-icon-arrowthickstop-1-e"></span> Importations</a></li>';

	/* Statistiques */
	if ( $pagemenuadm=="sta" ) { $affmenu_adm .= '<li class="actif">'; } else { $affmenu_adm .= '<li>'; }
	$affmenu_adm .= '<a href="'.$doss.'stats/admin.php" title="Gestion des statistiques"><span class="ui-icon ui-icon-signal"></span> Statistiques</a></li>';

}

$affmenu_adm .= '</ul><br/>';
echo $affmenu_adm;

// Bas du menu

$affmenubas_adm = '<ul id="ad-menubas">';
$affmenubas_adm .= '<li class="retoursite"><span class="ui-icon ui-icon-person"></span> <a href="'.$root_site.'espace_personnel.html" title="Mon espace perso">Mon espace personnel</a></li>';
$affmenubas_adm .= '<li class="retoursite"><span class="ui-icon ui-icon-arrowreturnthick-1-w"></span> <a href="'.$root_site.'" title="Retour au site">Retour au site</a></li>';
$affmenubas_adm .= '<li><a href="'.$root_site.'01_include/connexion_deconnexion.php" title="Déconnexion">Déconnexion</a></li>';
$affmenubas_adm .= '</ul><br/>';
echo $affmenubas_adm;
?>
