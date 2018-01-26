<?php
	// include header
	$titre_page = "Qu’est-ce que le projet Ensemble ici ? un outil à s'approprier !";
	$titre_page_bleu = "Un outil à s'approprier !";
	$meta_description = "Qu’est-ce que le projet Ensemble ici ? un outil à s'approprier ! Ensemble ici : Tous acteurs de la vie locale.";

	/* $ajout_header = <<<AJHE
AJHE;*/
	include ('01_include/structure_header.php');

?>
      <div id="colonne2">                  

	<h2>Ensemble ici : un outil à s'approprier !</h2>

<p>A l'origine d'Ensemble ici, une idée simple : c'est mieux ensemble que tout seul ! Surtout quand on vit à la campagne ! Ensemble ici est une initiative citoyenne, un outil à saisir, une façon de s'approprier internet pour se faciliter la vie ensemble, une résistance à l'individualisme et à la crise par davantage de réciprocité, d'écologie, de solutions locales et de solidarité.</p>

<p>L'idée d'Ensemble ici, elle est venue tout naturellement, à partir des besoins de chacun. Et c'est ainsi que se sont rapprochés ceux qui se battent pour animer et informer sur notre territoire des Baronnies et au-delà (de Nyons à Buis-les-Baronnies, en passant par Rémuzat et Montbrun-les-bains). Un collectif engagé s'est constitué pour porter le projet : l'Association pour l'animation du Haut-Nyonsais (Curnier), le Tam-tam des Baronnies (Buis-les-Baronnies), Africultures (Les Pilles) ainsi que l'Association pour le Développement des Communautés rurales (DECOR, Les Pilles) qui s'occupe du développement informatique.</p>

<p>Le site est entièrement géolocalisé et affiche ainsi en priorité l'information la plus proche de votre lieu de vie ou d'intérêt. Les habitants et acteurs de la vie locale peuvent présenter leurs activités, leurs productions, leurs initiatives : elles apparaissent dans les pages concernées (les différentes « vies ») mais aussi dans des répertoires. L'agenda donne l'actualité sociale et culturelle.</p>

<p>Peu à peu, le site s'enrichira de fonctions permettant davantage d'échanges et de services : des forums thématiques et des forums-questions, des articles faisant du site un nouveau type de média citoyen, des annonces simples pour faciliter les contacts et les échanges, etc. A terme, le site Ensemble ici permettra ainsi tous les niveaux possibles de collaboration entre les habitants pour faciliter une vie sociale, culturelle, associative et professionnelle plus vivante et solidaire. Des espaces d'échanges et de propositions de services seront développés à partir des besoins de tous (babysitting, covoiturage local, recherche de bénévoles, troc et mise en commun d'outils, etc.). Chacun pourra intervenir à son niveau.</p>

<p>C'est ainsi que nous pourrons, ensemble, mettre en pratique les valeurs de l'économie sociale et solidaire, et améliorer par davantage d'information et de démarches collectives notre vie ici.</p>

<p>L'enjeu est aussi que nous échangions non seulement des informations mais des réflexions sur la vie locale. Les forums le permettront mais des articles spontanés le faciliteront aussi, pour une nouvelle forme de média citoyen : témoignages et débats, à vos plumes et claviers !</p>

<p><a href="<?php echo $root_site ?>02_medias/08_pdf/charte-ensembleici.pdf" title="Découvrez la charte Ensemble ici !">Découvrez la charte Ensemble ici !</a></p>


      </div>
    
<?php
	// Colonne 3
	// $affiche_articles = true;
	$affiche_publicites = true;
	include ('01_include/structure_colonne3.php');
	
	// Footer
	include ('01_include/structure_footer.php');
?>