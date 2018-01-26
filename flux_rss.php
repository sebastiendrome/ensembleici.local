<?php
$contenu_droite = contenu_colonne_droite("autres_pages");
//On récupère le titre et le contenu du bloc "animation"
/*$requete = "SELECT titre, contenu FROM contenu_blocs WHERE no=12 AND etat=1";
$tab_animation = execute_requete($requete);
$contenu = (count($tab_animation)>0)?$tab_animation[0]["contenu"]:"";*/
require_once("01_include/fonctions_rss.php"); //Pour lire notre propre flux rss
$contenu = '<div id="gbl_rss">';
	$contenu .= '<img src="img/rss.png" class="img_grand" />';
	$contenu .= '<h1>Ensemble-ici vous offre l\'actualité de ses évènements en temps réel</h1>';
	$contenu .= '<p>Pour être au courant en temps réel, Ensemble ici vous propose ses <strong>flux d\'information personnalisés</strong>. Webmasters, vous pouvez ainsi enrichir votre site web avec une actualité permanente.</p>';
	$contenu .= '<p>Intégrez le fil d\'information à votre site internet pour afficher l\'actualité de nos évènements en temps réel.</p>';
	$contenu .= '<p><input onfocus="this.select();" type="text" value="http://www.ensembleici.fr/flux_rss.xml" width="200" /></p>';
	$contenu .= '<p>Ou ajoutez les fils d\'information <strong>Ensemble-ici</strong> à vos pages d\'accueil personnalisées (sous Google, Yahoo, Netvibes, Webwag)</p>';
	$contenu .= '<p>';
		//$contenu .= '<a id="lien_flux_articles_google" href="http://www.google.com/ig/add?feedurl=http://www.ensembleici.fr/flux_rss.xml"><img src="img/add_google.gif" class="img_petit" /></a>';
		$contenu .= '<a id="lien_flux_articles_yahoo" href="http://add.my.yahoo.com/content?lg=fr&amp;url=http://www.ensembleici.fr/flux_rss.xml"><img src="img/add_yahoo.gif" class="img_petit"></a>';
		$contenu .= '<a id="lien_flux_articles_netvibes" href="http://www.netvibes.com/subscribe.php?url=http://www.ensembleici.fr/flux_rss.xml"><img src="img/add_netvibes.png"></a>';
		$contenu .= '<a id="lien_flux_articles_webmag" href="http://www.webwag.com/wwgthis.php?url=http://www.ensembleici.fr/flux_rss.xml"><img src="img/add_webmag.gif"></a>';
	$contenu .= '</p>';
	$contenu .= '<div id="bloc_rss">';
		$contenu .= '<h1>Exemple de flux RSS : derniers événements</h1>';
		$contenu .= RSS_Display($root_site."flux_rss.xml", 15);
	$contenu .= '</div>';
$contenu .= '</div>';

$ligne1 = array(array("class"=>"grand_moyen","id"=>"colonne_gauche","contenu"=>$contenu),array("class"=>"moyen_petit","id"=>"colonne_droite","contenu"=>$contenu_droite));
$lignes = array(array("lignes"=>$ligne1));
?>
