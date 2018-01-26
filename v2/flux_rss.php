<?php
	// include header
	$titre_page = "Flux RSS";
	$meta_description = "Flux RSS du site Ensemble Ici. Ensemble ici : Tous acteurs de la vie locale.";

	/* $ajout_header = <<<AJHE
AJHE;*/
	include ('01_include/structure_header.php');

?>
      <div id="colonne2">
      	<div id="gbl_rss">
            <img src="img/rss.png" class="img_grand" />
            <h2>Ensemble-ici vous offre l'actualité de ses évènements en temps réel</h2>
            <p>Pour être au courant en temps réel, Ensemble ici vous propose ses <strong>flux d'information personnalisés</strong>. 
                Webmasters, vous pouvez ainsi enrichir votre site web avec une actualité permanente. </p>
    
            <p>Intégrez le fil d'information à votre site internet pour afficher l'actualité de nos évènements en temps réel.</p>
            <p><input type="text" value="http://www.ensembleici.fr/flux_rss.xml" width="200" /></p>
            
            <p>Ou ajoutez les fils d'information <strong>Ensemble-ici</strong> à vos pages d'accueil personnalisées (sous Google, Yahoo, Netvibes, Webwag)</p>
            <p>
                <a id="lien_flux_articles_google" href="http://www.google.com/ig/add?feedurl=http://www.ensembleici.fr/flux_rss.xml"><img src="img/add_google.gif" class="img_petit" /></a>
            
                <a id="lien_flux_articles_yahoo" href="http://add.my.yahoo.com/content?lg=fr&amp;url=http://www.ensembleici.fr/flux_rss.xml"><img src="img/add_yahoo.gif" class="img_petit"></a>    
                
                <a id="lien_flux_articles_netvibes" href="http://www.netvibes.com/subscribe.php?url=http://www.ensembleici.fr/flux_rss.xml"><img src="img/add_netvibes.png"></a>	
                
                <a id="lien_flux_articles_webmag" href="http://www.webwag.com/wwgthis.php?url=http://www.ensembleici.fr/flux_rss.xml">
                <img src="img/add_webmag.gif"></a>
            </p>
			<?php
            require_once("01_include/fonctions_rss.php");
            echo "<div id=\"bloc_rss\">";
			echo "<h2>Exemple de flux RSS : derniers événements</h2>";
            echo RSS_Display("http://www.ensembleici.fr/flux_rss.xml", 15);
            echo "</div>";
            ?>
        </div>
      </div>
    
<?php
	// Colonne 3
	// $affiche_articles = true;
	$affiche_publicites = true;
	include ('01_include/structure_colonne3.php');
	
	// Footer
	include ('01_include/structure_footer.php');
?>