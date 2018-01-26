<?php
	// include header
	$titre_page = "Soutenez le projet « Ensemble ici » et faites perdurer son site internet grâce à vos dons !";
	$titre_page_bleu = "Faire un don";
	$meta_description = "Soutenez le projet « Ensemble ici » et faites perdurer son site internet grâce à vos dons ! Ensemble ici : Tous acteurs de la vie locale.";

	/* $ajout_header = <<<AJHE
AJHE;*/
	include ('01_include/structure_header.php');

?>
      <div id="colonne2">                  

	<h2>Soutenez le projet « Ensemble ici » et faites perdurer son site internet grâce à vos dons !</h2>

<div class="blocC">
	<p>Pour l'année 2012-2013, l'association DECOR a besoin de rassembler des fonds pour subvenir aux besoins du site internet « Ensemble ici » et à ses frais administratifs. </p>

<p class="signature"><strong>Olivier Barlet</strong><br/>
Président de l'association "DECOR"<br/>
(Association porteuse administrative et technique du projet « Ensemble ici »</p>
 </div>

        <div id="faire_un_don">
            <a href="faire_un_don.html"><img alt="Faire un don" src="<?php echo $root_site; ?>img/faire_un_don.png" /></a>
        </div>

<h2>Faire un don par courrier</h2>

<p>Envoyez votre chèque bancaire à l'ordre de "Association DECOR" porteur administratif et technique du projet, à l'adresse suivante :<br/>
Association DECOR (développement des communautés rurales) <br/>
Route de nyons <br/>
26110 Les Pilles </p>

<p>N'oubliez pas de nous fournir vos coordonnées si vous souhaitez que votre prénom (et code postal) figure dans la liste des donateurs.</p>
<p>Si vous souhaitez que votre don reste anonyme, vous pouvez nous laisser un petit mot à cet effet dans votre courrier. Egalement, l'absence de vos coordonnées dans le courrier sera considérée comme un don anonyme.</p>
 
 
<h2>Faire un don en ligne (sécurisé par Paypal)<sup>*</sup></h2>

<p>Cliquez sur le bouton ci-dessous pour être redirigé(e) vers la page Paypal et effectuer votre don.</p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="W5LHPNJCCAKUE">
<p class="boutons">
  <input type="image" 
src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_donateCC_LG.gif" 
border="0" name="submit" alt="PayPal - la solution de paiement en ligne 
la plus simple et la plus sécurisée !">
<img alt="" border="0" 
src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" 
height="1">
</p>
</form>

<p>Notez qu'il n'est pas obligatoire de posséder un compte paypal pour effectuer un don.<br/>
Si vous souhaitez effectuer un don anonyme, saisissez les mots "don anonyme" dans le petit encars 'Vous souhaitez nous laisser un message ?" lors de la confirmation de votre don.</p>


<p><sup>*</sup> des frais sont prélevés par Paypal sur vos dons effectués en ligne (25 centimes + 1,4% à 3,4% du montant). L'entreprise Paypal se rémunère ainsi pour ce service fourni gratuitement à ensembleici.fr.</p>


      </div>
    
<?php
	// Colonne 3
	// $affiche_articles = true;
	$affiche_publicites = true;
	include ('01_include/structure_colonne3.php');
	
	// Footer
	include ('01_include/structure_footer.php');
?>