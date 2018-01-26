<?php
	include_once "_session_start.php";
	mb_internal_encoding("UTF-8");
	$cle_cryptage="JjEJb5eV30EBNLFtm2wNrk9afjz612B6fxVfo7jQ86ZybNoXuQ"; // = Salt

	// URL root du site pour redirections
	$est_dev = true; // <= Modifier cette ligne
	$root_site_prod = "http://www.ensembleici.fr/";
//        $root_site_prod = "http://localhost/EnsembleIci/";
//        $root_site_prod = "http://ensemble.envol2.serveur-dedie.fr/";
//	$root_site_dev = $root_site_prod."00_dev/";
        $root_site_dev = "http://www.ensembleici.fr/";
//        $root_site_dev = "http://ensemble.envol2.serveur-dedie.fr/";
	$root_site = (!$est_dev)?$root_site_prod:$root_site_dev;
	$root_admin = $root_site."gestion/";
//	$root_serveur_prod = "/home/ensemble/www/";
//	$root_serveur_dev = $root_serveur_prod."00_dev/";
//        $root_serveur_prod = "/media/www-dev/public/EnsembleIci/";
//        $root_serveur_dev = "/media/www-dev/public/EnsembleIci/";
        $root_serveur_prod = "/home/ensemble/";
        $root_serveur_dev = "/home/ensemble/";
	$root_serveur = (!$est_dev)?$root_serveur_prod:$root_serveur_dev;
	
	
	// Ville par défaut
	$id_ville_defaut = 9568;
	//départements autorisés
	$departements_autorise ="26,07,84,05";
	
	// Emails
	$email_admin="contact@ensembleici.fr";
	$email_admin_2="ensembleici@gmail.com";
	$email_forum = "forum@ensembleici.fr";
	$email_newsletter = "newsletter@ensembleici.fr";
	$emails_header = file_get_contents($root_site."01_include/template_mail_header.php");
	$emails_footer = file_get_contents($root_site."01_include/template_mail_footer.php");
	$table_user="utilisateur";

	// Nombre d'évenements et de structures à afficher sur les page ville et home
	$nb_evts_home = $nb_struct_home = $nb_pa_home = 4;
	$nb_evts_list = $nb_struct_list = 10;
	$nb_pa_list = 20;

	// Géolocalisation
	$rayon = 500; // rayon  de recherche (en km)
	$rayon_terre = 6371;  // rayon de la terre

	require_once 'fonctions.php';
	require_once '_connect.php';
?>
