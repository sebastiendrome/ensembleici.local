<?php
						echo '</div>'; //contenu_page
						
					echo '</div>';
				echo '</div>'; //contenu
				
				//FOOTER
                                $territoire = 1;
                                if (isset($_SESSION["utilisateur"]["territoire"])) {
                                    $territoire = $_SESSION["utilisateur"]["territoire"];
                                }
                                $requete = "SELECT titre, contenu FROM contenu_blocs WHERE etat=1 AND ref=2 AND territoires_id = ".$territoire;
				$tab_bloc = execute_requete($requete);
				if(!empty($tab_bloc)){
					if(!empty($tab_bloc[0]["titre"]))
						$qu_est_ce_que_ei = '<h3>'.$tab_bloc[0]["titre"].'</h3>';
					if(!empty($tab_bloc[0]["contenu"]))
						$qu_est_ce_que_ei .= '<p>'.$tab_bloc[0]["contenu"].'</p>';
				}
                                
                                $requetepart = "SELECT titre, contenu FROM contenu_blocs WHERE etat=1 AND ref=22 AND territoires_id = ".$territoire;
				$tab_blocpart = execute_requete($requetepart);
                                
				echo '<footer id="footer">';
					echo '<div>';
					
						echo '<div id="qu_est_ce_que_ei">';
							echo '<div>';
								echo $qu_est_ce_que_ei;
								echo '<a href="explications.html" title="En savoir plus sur le projet"><input type="button" value="En savoir plus sur le projet" class="ico fleche" /></a>';
								echo '<div id="licence">';
				    				echo '<a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/fr/"><img id="creativecommons" alt="Licence Creative Commons" src="'.$root_site.'img/creativecommons-88x31.png" /></a>';
				    				echo '<p>Sauf mention contraire, le contenu de ce site est mis à disposition selon les termes de la <a target="_blank" rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/fr/">Licence Creative Commons Paternité - Partage à l&#39;Identique 3.0 France</a>.</p>';
			   					echo '</div>';
								/*echo '<div id="faire_un_don">';
									echo '<a href="faire_un_don.html"><img alt="Faire un don" src="'.$root_site.'img/faire_un_don.png" /></a>';
								echo '</div>';*/
							echo '</div>';
						echo '</div>';
						
						echo '<div id="partenaires">';
							echo '<div>';
								echo 'partenaires :<br />';
                                                                echo $tab_blocpart[0]["contenu"];
//								echo '<a href="http://www.iciplaceslibres.org/" target="_blank" title="Places Libres">';
//								echo '<img src="02_medias/07_pubs/09-placeslibres.jpg" alt="Places Libres" width="100" /></a>';
//
//								echo '<a href="http://mscurnier.canalblog.com/" target="_blank" title="assohautnyonsais">';
//								echo '<img src="02_medias/07_pubs/01-assohautnyonsais.jpg" alt="assohautnyonsais" width="100" /></a>';
//
//								echo '<a href="http://www.culture-provence-baronnies.fr/" target="_blank" title="culture-provence-baronnies">';
//								echo '<img src="02_medias/07_pubs/02-culturebaronnies.jpg" alt="culture-provence-baronnies" width="100" /></a>';
//
//								echo '<a href="http://www.buis-les-baronnies.com/" target="_blank" title="buis-les-baronnies">';
//								echo '<img src="02_medias/07_pubs/03-tamtam.jpg" alt="buis-les-baronnies" width="100" /></a>';
//							
//								//echo '<a href="http://www.radio-m.fr/" target="_blank" title="Radio M">';
//								//echo '<img src="02_medias/07_pubs/10-radiom.jpg" alt="Radio M" width="100" /></a>';
//
//								echo '<a href="http://www.africultures.com/" target="_blank" title="Africultures">';
//								echo '<img src="02_medias/07_pubs/11-africultures.jpg" alt="Africultures" width="100" /></a>';
//
//								echo '<a href="http://solidarnyons.jimdo.com/" target="_blank" title="SolidarNyons">';
//								echo '<img src="02_medias/07_pubs/12-solidarnyons.jpg" alt="SolidarNyons" width="100" /></a>';
//
//								echo '<a href="http://www.pole-numerique.fr/" target="_blank" title="Pôle numérique">';
//								echo '<img src="02_medias/07_pubs/13-polenumerique.png" alt="Pôle numérique" width="100" /></a>';
//
//								echo '<a href="http://www.la-cigale.com/" target="_blank" title="La Cigale">';
//								echo '<img src="02_medias/07_pubs/14-lacigale.jpg" alt="La Cigale" width="100" /></a>';
							echo '</div>';
						echo '</div>';
					
						echo '<div id="menu_footer">';
							echo '<a href="'.$NOM_VILLE_URL.'.'.$ID_VILLE.'.html">Accueil</a>';
							echo '<a href="flux-rss.html">Flux RSS</a>';
							echo '<a href="archives-lettres-informations.html">Lettres d\'infos</a>';
							echo '<a href="partenaires.html">Partenaires</a>';
							echo '<a href="plan-du-site.html">Plan du site</a>';
							echo '<a href="contact.html">Contactez-nous</a>';
							echo '<a href="faire-un-don.html">Faire un don</a>';
							echo '<a href="signaler-un-abus.html">Signaler un abus</a>';
							echo '<a href="mentions-legales.html">Mentions légales</a>';
						echo '</div>';
						
					echo '</div>';
				echo '</footer>';
				
				
			echo '</div>'; //page
                
			?>
<!--<script>
// Google analytics 
var _gaq=[['_setAccount','UA-32761608-1'],['_trackPageview']];

(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
s.parentNode.insertBefore(g,s)}(document,'script'));
/*var _gaq=[['_setAccount','UA-32761608-1'],['_trackPageview']];
function google_analytics(d,t){
	console.log("analytic");
	var a = new Date();
	if(d.getElementById(t+'google_analytic')!=null){d.getElementById(t+'google_analytic').parentNode.removeChild(d.getElementById(t+'google_analytic'));}
	var g=d.createElement(t), s=d.getElementsByTagName(t)[0];
	g.id=t+'google_analytic';
	g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js?t='+a.getTime();
	s.parentNode.insertBefore(g,s);
}
google_analytics(document,'script');*/

</script>-->
<div id="disclaimer" style="display: none;" class="disclaimer">
    <div style="margin-right: 30px; margin-top: 5px; text-align: right; font-style: normal; cursor: pointer;" id="close_dislaimer">X</div>
    <div style="margin-left: 10%; margin-right: 10%; margin-top: 0px; text-align: left;">
    Soutenir Ensemble Ici par vos dons et adhésions, c'est soutenir une plateforme numérique et une équipe d’animation au service de la vie locale et 
    des initiatives.Nous avons besoin de vous pour maintenir et faire évoluer le projet.
    </div>
    <div style="margin-left: 10%; margin-right: 10%; margin-top: 20px; margin-bottom: 25px; text-align: right;">
        Je soutiens et participe à l’association locale par mon <a name="link_don" target="_blank" href="<?= $_SESSION['utilisateur']['url_adhesion'] ?>" class="btn btn-success">ADH&Eacute;SION</a> 
        <span style="margin-left: 30px;">et/ou</span><span style="margin-left: 30px;">Je soutiens par un</span> <a name="link_don" target="_blank" href="<?= $_SESSION['utilisateur']['url_don'] ?>"  class="btn btn-success">DON</a> 
    </div>
</div>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', '<?= $_SESSION["utilisateur"]["code_ua"] ?>', 'auto');
  ga('send', 'pageview');

</script>

	</body>
</html>
