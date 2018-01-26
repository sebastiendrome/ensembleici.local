<?php
$requete_menu = "SELECT * FROM  administrationMenu JOIN droit_administrationMenu ON droit_administrationMenu.no_administrationMenu=administrationMenu.no WHERE droit_administrationMenu.no_droit=:no_droit";
$tab_menu = execute_requete($requete_menu,array(":no_droit"=>$_SESSION["droit"]["no"]));
$menu = '';
for($i=0;$i<count($tab_menu);$i++){
	$menu .= '<a href="?page='.$tab_menu[$i]["url_rewrite"].'&no=" id="page_'.$tab_menu[$i]["url_rewrite"].'" class="item_menu '.$tab_menu[$i]["url_rewrite"].'">'.$tab_menu[$i]["libelle"].'</a>';
}

$contenu = '<div class="bloc moyen">';
	$contenu .= '<div>';
		$contenu .= '<h1>Bienvenue '.$_SESSION["utilisateur"]["pseudo"].' !</h1>';
		$contenu .= '<p>Voici votre espace '.$_SESSION["droit"]["libelle"].' pour le territoire '.$LIBELLE_TERRITOIRE.'.</p>';
		$contenu .= '<p>Pour commencer, choisissez une rubrique dans le menu ci-dessus.</p>';
		$contenu .= '<h2>Une toute nouvelle administration !</h2>';
	$contenu .= '<p>Voici la nouvelle administration d\'ensemble ici! Une nouvelle interface plus légère et plus intuitive. Désormais compatible sur tablette et smartphone, consultez l\'administration d\'ensemble ici depuis n\'importe où!</p>';
	$contenu .= '</div>';
$contenu .= '</div>';
$contenu .= '<div class="bloc moyen">';
	$contenu .= '<div>';
		$contenu .= '<h1>Nouveautés</h1>';
		$contenu .='<ul>';
			$contenu .= '<li>Une interface plus simple d\'utilisation.</li>';
			$contenu .= '<li>Une interface plus légère.</li>';
			$contenu .= '<li>Mise en place d\'un système de recherche.</li>';
			$contenu .= '<li>Mise en place d\'un système de pagination.</li>';
			$contenu .= '<li>Une interface compatible tablettes et smartphones.</li>';
			$contenu .= '<li>Ouverture d\'un espace éditeur.</li>';
			$contenu .= '<li>Ouverture de l\'espace éditorial.</li>';
			$contenu .= '<li>Ouverture de l\'espace forum.</li>';
			$contenu .= '<li>Enregistrement des données en temps réel.</li>';
			$contenu .= '<li>Les images png et gif sont maintenant accepté.</li>';
			$contenu .= '<li>Les images peuvent dépasser 1Mo et seront automatiquement retravaillées par le serveur.</li>';
		$contenu .= '</ul>';
	$contenu .= '</div>';
$contenu .= '</div>';
$page = array("menu"=>$menu,"contenu"=>$contenu);
?>
