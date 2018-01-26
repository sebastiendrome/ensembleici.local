  <div class="clear"></div>

      

  </div>

  

  <div id="footer-container" class="wrapper">

    <footer>



      <div id="txt-footer" class="colonne description">

<?php

require_once ('01_include/_connect.php');



// Affichage du texte de bas de page (a propos...)



$idbloc = 2;

$sql_bloc="SELECT titre, contenu

            FROM `contenu_blocs`

            WHERE etat = :etat

            AND no = :idbloc";

$res_bloc = $connexion->prepare($sql_bloc);

$res_bloc->execute(array(':idbloc'=>$idbloc, ':etat'=>1));

$tab_bloc = $res_bloc->fetch(PDO::FETCH_ASSOC);

if ((!empty($tab_bloc["titre"]))||(!empty($tab_bloc["contenu"])))

{

  if (!empty($tab_bloc["titre"])) echo "<h3>".$tab_bloc["titre"]."</h3>";

    if (!empty($tab_bloc["contenu"])) echo "<div class=\"contenu\">".nl2br($tab_bloc["contenu"])."</div>";

    echo "<div class=\"actions\">

      <a href=\"explications.html\" title=\"En savoir plus sur le projet\" class=\"boutonbleu ico-fleche\">En savoir plus sur le projet</a>

    </div>";

}

?>

          <div id="licence">
            <a target="_blank" rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/fr/"><img alt="Licence Creative Commons" src="<?php echo $root_site; ?>img/creativecommons-88x31.png" /></a>Sauf mention contraire, le contenu de ce site est mis à disposition selon les termes de la <a target="_blank" rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/fr/">Licence Creative Commons Paternité - Partage à l&#39;Identique 3.0 France</a>.
          </div>

          <div id="faire_un_don" class="colonne">
			<a href="faire_un_don.html"><img alt="Faire un don" src="<?php echo $root_site; ?>img/faire_un_don.png" /></a>
		  </div>

        </div>



		<div id="Covoiturage" class="colonne" style="position:relative">
			<!--<center><script type="text/javascript" src="http://www.ecovoiturage0726.fr/ecoluSearch.js?title=Recherche+de+trajets+de+covoiturage" ></script></center>-->
		</div>
		
		<div id="partenaires" class="colonne">
          Partenaires : <br/>

          <a href="http://www.iciplaceslibres.org/" target="_blank" title="Places Libres">
          <img src="02_medias/07_pubs/09-placeslibres.jpg" alt="Places Libres" width="100" /></a>  
		  
		  <a href="http://mscurnier.canalblog.com/" target="_blank" title="assohautnyonsais">
          <img src="02_medias/07_pubs/01-assohautnyonsais.jpg" alt="assohautnyonsais" width="100" /></a> 
		  
		  <a href="http://www.culture-provence-baronnies.fr/" target="_blank" title="culture-provence-baronnies">
          <img src="02_medias/07_pubs/02-culturebaronnies.jpg" alt="culture-provence-baronnies" width="100" /></a> 
		  
		  <a href="http://www.buis-les-baronnies.com/" target="_blank" title="buis-les-baronnies">
          <img src="02_medias/07_pubs/03-tamtam.jpg" alt="buis-les-baronnies" width="100" /></a> 
		  <!--
		  <a href="http://www.radio-m.fr/" target="_blank" title="Radio M">
          <img src="02_medias/07_pubs/10-radiom.jpg" alt="Radio M" width="100" /></a> 		-->
		
		  <a href="http://www.africultures.com/" target="_blank" title="Africultures">
          <img src="02_medias/07_pubs/11-africultures.jpg" alt="Africultures" width="100" /></a> 
          
          <a href="http://solidarnyons.jimdo.com/" target="_blank" title="SolidarNyons">
          <img src="02_medias/07_pubs/12-solidarnyons.jpg" alt="SolidarNyons" width="100" /></a>
          
          <a href="http://www.pole-numerique.fr/" target="_blank" title="Pôle numérique">
          <img src="02_medias/07_pubs/13-polenumerique.png" alt="Pôle numérique" width="100" /></a>
          
          <a href="http://www.la-cigale.com/" target="_blank" title="La Cigale">
          <img src="02_medias/07_pubs/14-lacigale.jpg" alt="La Cigale" width="100" /></a>
		</div>

        

        <ul id="menufoot">
          <li><a href="<?php echo $root_site; ?>" title="Accueil">Accueil</a></li>
          <li><a href="flux_rss.php" title="Partenaires">Flux RSS</a></li>
          <li><a href="lettreinfo_archives.php" title="Archives des lettres d'information">Lettres d'infos</a></li>
          <li><a href="partenaires.html" title="Partenaires">Partenaires</a></li>
          <li><a href="plan_site.html" title="Plan du site">Plan du site</a></li>
          <li><a href="contact.html" title="Contactez-nous">Contactez-nous</a></li>
          <li><a href="signaler_un_abus.html" title="Signaler un abus">Signaler un abus</a></li>
          <li><a href="mentions_legales.html" title="Mentions légales">Mentions légales</a></li>
        </ul>
    </footer>
  </div>

  <script>
    /* Google analytics */
    var _gaq=[['_setAccount','UA-32761608-1'],['_trackPageview']];
    (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
    g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
    s.parentNode.insertBefore(g,s)}(document,'script'));
  </script>
</body>
</html>
