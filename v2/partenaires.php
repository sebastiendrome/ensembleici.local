<?php
	$titre_page = "Partenaires";
	$meta_description = "Partenaires du site Ensemble Ici. Ensemble ici : Tous acteurs de la vie locale.";
	/* $ajout_header = <<<AJHE
AJHE;*/
	include ('01_include/structure_header.php');

?>
  <div id="colonne2">

<h1>Fonctionnement</h1>

<p>« Ensemble ici » est un collectif  associant habitants, acteurs locaux, structures associatives et  citoyennes. Le groupe d'origine comprend l'association pour  l'Animation Sociale du Haut-Nyonsais (Curnier), le Tam-Tam des  Baronnies (Buis-les-Baronnies), l'association Africultures (Les  Pilles) et l'association pour le DEveloppement des COmmunautés  Rurales (DECOR, Les Pilles). </p>
<p>Initiatrice de ce projet, l'association  DECOR se positionne comme le principal administrateur et prestataire  technique.</p>
<p>Bien sûr, toute personne animée par  un projet de portail / média citoyen local est invitée à  participer au collectif « Ensemble ici » ou rejoindre le réseau de  partenaires du projet. </p>
<p>A bon entendeur !</p>
<p><a title="Contactez le collectif !" href="contact.html">Contactez le collectif</a> 
- <a href="<?php echo $root_site ?>02_medias/08_pdf/charte-ensembleici.pdf" title="Découvrez la charte Ensemble ici !">Découvrez la charte Ensemble ici !</a></p>

<h1>Partenaires</h1>

<p>Les associations et acteurs du  territoire qui encouragent et soutiennent la dynamique «  Ensemble ici » : </p>
<h2>Communauté de communes du Val d’Eygues :</h2>
<ul>
  <li>L’association « A saute-page » -  Curnier</li>
  <li>L’association Chrysalide ASP26 –  Curnier</li>
  <li>L’association « Les cent ciels » - Arpavon</li>
  <li>Aube  nouvelle – Condorcet</li>
  <li>Axformix – La Basse Bégude</li>
  <li>Terre  et Expression – Les Pilles</li>
  <li>Shiatsu Connection et le Centre  de Yoga Iyengar du Nyonsais – Les Pilles</li>
  <li>Association ouiE  – Condorcet</li>
  <li> Lucarne Productions (As. Alaïl) –  Curnier</li>
  <li>Association « Places libres » - Nyons</li>
  <li> Association  Passerelle bleue – Curnier</li>
  <li>Théâtre de l’Aube –  Condorcet</li>
  <li>Association Les Pilanthropes – Les  Pilles</li>
  <li>Association Evénement’ciel –  Sainte-Jalle</li>
  <li>Solidar'Nyons - Nyons</li>
  <li>L’association  de l’Individu – Sahune</li>
</ul>
<h2>Communauté de communes de Buis les  Baronnies</h2>
<ul>
  <li>Association familiale de  Buis-les-Baronnies - Buis-les-Baronnies</li>
  <li>Tam-Tam des  Baronnies - Buis-les-Baronnies</li>
</ul>
<h2>Mais aussi !</h2>
<ul>
  <li>Pays Une autre provence</li>
  <li>Ca bouge à Clansayes ! –  Clansayes</li>
  <li>Fédération Toulourenc Culture Commune (FTCC) -  Montbrun-les-bains</li>
  <li>AD Concepts - Montbrun-les-bains</li>
  <li>Comité culturel et festif de  Reilhanette - Reilhanette</li>
  <li>Comité des fêtes de  Mollans-sur-Ouvèze - Mollans-sur-Ouvèze </li>
  <li>Association l’Arsène -  Montbrun-les-bains</li>
  <li>Syndicat mixte des Baronnies  Provençales - Sahune</li>
</ul>


  </div>

<?php
	// Colonne 3 manuelle
	// $affiche_articles = true;
	// $affiche_publicites = true;
	// include ('01_include/structure_colonne3.php');
?>

      <div id="colonne3">
        <aside>
		<div class="blocA">
			<h1>Bannières</h1>
			<p>Nous vous proposons plusieurs codes à ajouter à l'emplacement que vous le souhaitez dans vos pages internet. Pour récupérer l'un des codes, il vous suffit de faire un copier-coller.</p><br/>
			<h2>Bannière au format 468 x 60 px :</h2>
			<a href="img/banniere-468x60.jpg" class="agrandir" title="Bannière au format réel"><img src="img/banniere-468x60.jpg" width="260" alt=" Bannière 468x60" /></a><br/>
			<textarea class="code_banniere"><!--debut du code à ajouter dans votre page --><a href="http://www.ensembleici.fr" title="Ensemble ici : Votre portail d'information locale participatif"><img src="http://www.ensembleici.fr/img/banniere-468x60.jpg" alt="Ensemble ici : Votre portail d'information locale participatif" width="468" height="60" /></a><!--fin du code à ajouter dans votre page --></textarea>
			</p><br/>
			<h2>Bannière au format 250 x 250 px :</h2>
			<a href="img/banniere-250x250.jpg" class="agrandir" title="Bannière au format réel"><img src="img/banniere-250x250.jpg" width="250" alt=" Bannière 250x250" /></a><br/>
			<textarea class="code_banniere"><!--debut du code à ajouter dans votre page --><a href="http://www.ensembleici.fr" title="Ensemble ici : Votre portail d'information locale participatif"><img src="http://www.ensembleici.fr/img/banniere-250x250.jpg" alt="Ensemble ici : Votre portail d'information locale participatif" width="250" height="250" /></a><!--fin du code à ajouter dans votre page --></textarea></p>
			
			<script>
				jQuery.fn.selText = function() {
				    var obj = this[0];
				    if ($.browser.msie) {
					var range = obj.offsetParent.createTextRange();
					range.moveToElementText(obj);
					range.select();
				    } else if ($.browser.mozilla || $.browser.opera) {
					var selection = obj.ownerDocument.defaultView.getSelection();
					var range = obj.ownerDocument.createRange();
					range.selectNodeContents(obj);
					selection.removeAllRanges();
					selection.addRange(range);
				    } else if ($.browser.safari) {
					var selection = obj.ownerDocument.defaultView.getSelection();
					selection.setBaseAndExtent(obj, 0, obj, 1);
				    }
				    return this;
				}
				$(function() {
					$(".agrandir").colorbox();
					$(".code_banniere").click(function() {
					    $(this).selText().addClass("selected");
					});
				});
			</script>
		</div>
        </aside>       
      </div>
<?php
	// Footer
	include ('01_include/structure_footer.php');
?>