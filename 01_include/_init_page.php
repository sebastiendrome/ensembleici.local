<?php
if($ID_VILLE>0){
	if($PAGE_COURANTE=="editorial"||$PAGE_COURANTE=="agenda"||$PAGE_COURANTE=="petite-annonce"||$PAGE_COURANTE=="structure"||$PAGE_COURANTE=="forum")
		include $root_serveur."page.php";
	else{
		if($PAGE_COURANTE=="accueil")
			include $root_serveur."accueil.php";
		else if($PAGE_COURANTE=="espace-personnel")
			include $root_serveur."espace_personnel.php";
			else if($PAGE_COURANTE=="espace-personnel-dev") //TODO SUPPRIMER CETTE CONDITIONNELLE
				include $root_serveur."espace_personnel2.php";
		else if($PAGE_COURANTE=="recherche")
			include $root_serveur."recherche.php";
		else if($PAGE_COURANTE=="animation")
			include $root_serveur."animation.php";
		else if($PAGE_COURANTE=="vie-du-projet")
			include $root_serveur."vie_du_projet.php";
		else if($PAGE_COURANTE=="guide-utilisation")
			include $root_serveur."guide_utilisation.php";
		else if($PAGE_COURANTE=="les-cookies")
			include $root_serveur."les_cookies.php";
		else if($PAGE_COURANTE=="mentions-legales")
			include $root_serveur."mentions_legales.php";
		else if($PAGE_COURANTE=="signaler-un-abus")
			include $root_serveur."signaler_un_abus.php";
		else if($PAGE_COURANTE=="faire-un-don")
			include $root_serveur."faire_un_don.php";
		else if($PAGE_COURANTE=="contact")
			include $root_serveur."contact.php";
		else if($PAGE_COURANTE=="plan-du-site")
			include $root_serveur."plan_du_site.php";
		else if($PAGE_COURANTE=="partenaires")
			include $root_serveur."partenaires.php";
		else if($PAGE_COURANTE=="archives-lettres-informations")
			include $root_serveur."archives_lettres_informations.php";
		else if($PAGE_COURANTE=="flux-rss")
			include $root_serveur."flux_rss.php";
		else if($PAGE_COURANTE=="explications")
			include $root_serveur."explications.php";
		else if($PAGE_COURANTE=="desinscription")
			include $root_serveur."desinscription.php";
		else
			include $root_serveur."accueil_sans_ville.php";
	}
}
else {
	include $root_serveur."accueil_sans_ville.php";
}
?>
