<?php
	// Affichage des pages villes
	session_name("EspacePerso");
	session_start();
	require ('01_include/_var_ensemble.php');
	require ('01_include/_connect.php');
	// Supprimer la ville enregistrée (supprimer le cookie)
	$changerville = intval($_GET["changerville"]);
	
	if ($changerville==1)setcookie("id_ville", "", time() - 365*24*3600,"/", null, false, true);
	// cookie expiré
	// Récupère la ville sélectionnée, depuis l'URL
	
	if (($_GET["id_ville"])&&(!empty($_GET["id_ville"])))
	{
		$id_ville = intval($_GET["id_ville"]);
		// Si lien du popup de choix de la ville
	
		if ((intval($_GET["choixville"]))&&($_GET["choixville"]==1))setcookie("id_ville", $id_ville, time() + 365*24*3600,"/", null, false, true);
	}

	// depuis le formulaire de recherche d'une ville (colorbox de l'index)
	elseif ((isset($_POST["rech_idville"]))&&(!empty($_POST["rech_idville"])))
	{
		$id_ville = intval($_POST["rech_idville"]);
	}

	// depuis le cookie
	elseif (($_COOKIE["id_ville"])&&(!empty($_COOKIE["id_ville"])))
	{
		$id_ville = intval($_COOKIE["id_ville"]);
	}

	// Sinon popup pour choisir la ville 
	if (((!$id_ville)||(empty($id_ville)))||($changerville))
	{
		$titre_ville = "Choisissez une ville...";

		$ajout_header = <<<AJHE
		<script type="text/javascript">
		  $(function() {
		    $.colorbox({
		      href:"ajax_choix_ville.php",
		      width:"550px",
		      onComplete : function() {
				$(this).colorbox.resize();
				$('#cboxOverlay').fadeOut(4000);
		      }
		    });
		  });
		</script>
AJHE;
		// On prend la ville par défaut (derrière la colorbox)
		$id_ville = $id_ville_defaut;
	}

	// Infos de la ville selectionnée
	if (!empty($id_ville))
	{
		$sql_ville="SELECT * FROM villes WHERE id = :idville";
		$res_ville = $connexion->prepare($sql_ville);
		$res_ville->execute(array(':idville'=>$id_ville));
		$tab_ville = $res_ville->fetch(PDO::FETCH_ASSOC);
		$titre_ville = $tab_ville["nom_ville_maj"];
		$cp_ville = $tab_ville["code_postal"];
		$titre_ville_url = $tab_ville["nom_ville_url"];
		$lat_ville = $tab_ville["latitude"];
		$lon_ville = $tab_ville["longitude"];
	}

	// Une vie sélectionnée ?
	if (($_GET["id_vie"])&&(!empty($_GET["id_vie"])))
	{
		$id_vie = intval($_GET["id_vie"]);
		// Récup nom pour titre
		$sql_v="SELECT * FROM vie WHERE no = :idvie";
		$res_v = $connexion->prepare($sql_v);
		$res_v->execute(array(':idvie'=>$id_vie));
		$tab_v = $res_v->fetch(PDO::FETCH_ASSOC);
		$titre_nomvie = $tab_v["libelle"];
		$nom_url_vie = $tab_v["libelle_url"];
	}
	else
	{
		$id_vie = 0;
	}

	// Un tag sélectionné ?
	if (($_GET["id_tag"])&&(!empty($_GET["id_tag"])))
	{
		$id_tag = intval($_GET["id_tag"]);
		$est_ss_tag = intval($_GET["ss_tag"]);

		// C'est un sous-tag ?
		if ($est_ss_tag)
		{
			// Récup nom pour titre
			$sql_t="SELECT I.libelle AS nom_vie, T.titre AS nom_tag
				FROM `sous_tag` T, `tag_sous_tag` A, vie_tag V, vie I
				WHERE T.no = :idtag
				AND T.no = A.no_sous_tag
				AND A.no_tag = V.no_tag
				AND V.no_vie = I.no";
			$res_t = $connexion->prepare($sql_t);
			$res_t->execute(array(':idtag'=>$id_tag));
			$tab_t = $res_t->fetch(PDO::FETCH_ASSOC);
			$titre_nomvie = $tab_t["nom_vie"];
			$titre_nomtag = $tab_t["nom_tag"];
		}
		else
		{
			// Récup nom pour titre
			$sql_t="SELECT I.libelle AS nom_vie, T.titre AS nom_tag
				FROM tag T, vie_tag V, vie I
				WHERE T.no = :idtag
				AND T.no = V.no_tag
				AND V.no_vie = I.no";
			$res_t = $connexion->prepare($sql_t);
			$res_t->execute(array(':idtag'=>$id_tag));
			$tab_t = $res_t->fetch(PDO::FETCH_ASSOC);
			$titre_nomvie = $tab_t["nom_vie"];
			$titre_nomtag = $tab_t["nom_tag"];			
		}
	}
	else
	{
		$id_tag = 0;
	}

	// Liens afficher tous les evenements / tout le répertoire (affichage spécifique)
	if ((intval($_GET["specif"]))&&($_GET["specif"]==1))
	{
		$aff_specifique = true;
		if ((intval($_GET["tous_evts"]))&&($_GET["tous_evts"]==1)) $tous_evts = true;
		if ((intval($_GET["tous_struct"]))&&($_GET["tous_struct"]==1)) $tous_struct = true;
		if ((intval($_GET["tous_pa"]))&&($_GET["tous_pa"]==1)) $tous_pa = true;
	}

	// Titre de la page
	
	if ($tous_evts)
		$titre_page = "Agenda de $titre_ville, $cp_ville (tous les évènements)";
	elseif ($tous_struct)
		$titre_page = "Répertoire de $titre_ville, $cp_ville (toutes les structures)";
	elseif ($tous_pa)
		$titre_page = "Petites annonces de $titre_ville, $cp_ville (toutes les petites annonces)";
	elseif ($id_vie)
		$titre_page = "$titre_nomvie, Agenda et répertoire de $titre_ville, $cp_ville";
	elseif ($id_tag)
		$titre_page = "$titre_nomtag ($titre_nomvie), Agenda et répertoire de $titre_ville, $cp_ville";
	else
	{
		// L'accueil de la ville
		$titre_page = "$titre_ville ($cp_ville) : Agenda et répertoire";
		$diaporama = true;
		$page_index = true;
		$affiche_articles = true;
	}

	// include header de la page
	$titre_page_bleu = $titre_ville;
	$meta_description = $titre_page.". Agenda et répertoire de $titre_ville sur Ensemble ici : Tous acteurs de la vie locale";
	$page_ville = true;

	// Bouton modifier
	if ($page_index)
	{
	$ajout_header .= <<<AJHE
<link rel="stylesheet" href="css/homepage.css">
AJHE;
	}
	include ('01_include/structure_header.php');

	// Home page
	if ($page_index) { ?>
	<div id="colonne2large" class="page_home">
		<div id="load">
			<img src="img/image-loader.gif" alt="Chargement en cours" />
		</div>
		<section>
			<div id="contenu-index">
	          	<?php 

				// EDITO
				$idbloc = 1;
				$sql_bloc="SELECT titre, contenu
				          FROM `contenu_blocs`
				          WHERE etat = :etat
				          AND no = :idbloc";
				$res_bloc = $connexion->prepare($sql_bloc);
				$res_bloc->execute(array(':idbloc'=>$idbloc, ':etat'=>1));
				$tab_bloc = $res_bloc->fetch(PDO::FETCH_ASSOC);
				if ((!empty($tab_bloc["titre"]))||(!empty($tab_bloc["contenu"])))
				{ ?>
					<div id="home-edito" class="bloc-home bloc-grs">
						<div class="titre"></div>
			            <?php 
			            	if (!empty($tab_bloc["titre"]))
			            		echo "<h3>".$tab_bloc["titre"]."</h3>";
			              	if (!empty($tab_bloc["contenu"])) 
			              		echo "<div class=\"contenu\">".nl2br($tab_bloc["contenu"])."</div>";
			            ?>
						<div id="home-editobas"></div>
						<div id="home-plusangle" title="Voir tout" class="infobulle-l"></div>
					</div>
					
					<div id="home-forum" class="bloc-access-forum">
						<div class="nouveau" style="position:relative;top:10px;margin-top:-10px;color:rgb(193,37,45);font-size:0.8em;font-weight:bold;vertical-align:middle;"><img style="vertical-align:middle;" src="<?php echo $root_site; ?>02_medias/01_interface/img_colorize.php?uri=ico_nouveau.png&c=193,37,45" width="28" /><span style="position:relative;top:2px;">Nouveau !</span><img src="<?php echo $root_site; ?>02_medias/01_interface/img_colorize.php?uri=ico_nouveau.png&c=193,37,45" width="28" style="vertical-align:middle;" /></div>
						<h3 style="text-align: center;">Les forums thématiques !</h3>
                    	<p style="text-indent:1em;">Pour s'exprimer, se retrouver ou s'associer sur un sujet commun, les forums sont là !</p><p style="text-indent:1em;">Habitants, associations, élus, échangez avec d'autres personnes en toute liberté !</p>
						<a class="boutonbleu ico-fleche" style="float:right;" href="<?php echo $root_site.$titre_ville_url.".".$id_ville.".tout.forums.html"; ?>">Accéder aux forums</a>	
					</div>
					
			<script type="text/javascript">
			// Redim bloc edito (voir tout)
			$(function() {
					$('#home-editobas,#home-plusangle').click(function() {
						var contenuib;
						var htactuelle = $('#home-edito').height(); // hauteur avant
						var httotale = $('#home-edito').css('height','auto').height(); // ht dépliée
						$('#home-edito').css('height',htactuelle+'px'); // retour ht actuelle
						if (htactuelle == 170) 
						{
							ht = httotale; // hauteur cible
							contenuib = 'Replier'; // texte infobulle
							$('#home-editobas').css('background-image',"url('../img/desc.gif')"); // icone bas
						}
						else
						{
							ht = 170;
							contenuib = 'Voir tout'; // texte infobulle
							$('#home-editobas').css('background-image',"url('../img/asc.gif')");
						}
					    $('#home-edito').animate({height: ht+'px'}, 400);
						
						// Modification infobulle
					    $('#home-plusangle').poshytip('disable');
					    $('#home-plusangle').poshytip({
					        content: contenuib,
						    showTimeout:0,
						    hideTimeout:0,
						    timeOnScreen:0,
						    className: 'infobulle-tip',
						    alignTo: 'target',
						    alignX: 'inner-left',
						    alignY: 'bottom',
						    offsetX: 0,
						    offsetY: 7
					    });
					    $('#home-plusangle').poshytip('enable');
					});
			});
			</script>

				<?php   
				}
				// PUB
				require ('01_include/affiche_pub.php');
				//echo '<div id="publicite1" class="blocA"><h2>Nouveauté !</h2><div class="contenuimg"><img src="02_medias/07_pubs/06-pa.png" alt="Nouveauté !"  width="266" /></div></div>';
				
				// AGENDA
				require ('01_include/affiche_agenda.php');
				// REPERTOIRE
				require ('01_include/affiche_repertoire.php');
				// PETITES ANNONCES
				require ('01_include/affiche_petiteannonce.php');

                  echo "<div class=\"clear\"></div>";
				?>
			</div>
		</section>
	</div>

	<?php 
	}
	else 
	{
		// Pages non Home
	?>
		<div id="colonne2" class="page_ville">
		<div id="load">
			<img src="img/image-loader.gif" alt="Chargement en cours" />
		</div>
			<section>
				<div id="contenu-index">
			<?php
			if (((!$aff_specifique)||(($aff_specifique)&&($tous_evts)))&&(!$est_ss_tag))
				// Affichage de l'age$nda
				include ('01_include/affiche_agenda.php');

			if ((!$aff_specifique)||(($aff_specifique)&&($tous_struct)))
				// Affichage du repertoire
				include ('01_include/affiche_repertoire.php');

			if ((!$aff_specifique)||(($aff_specifique)&&($tous_pa)))
				// Affichage des petites annonces
				include ('01_include/affiche_petiteannonce.php'); ?>

				</div> <?php // Fin contenu-index ?>
			</section>
		</div>
		<?php
			// Colonne 3
			$affiche_publicites = true;
			include ('01_include/structure_colonne3.php');
	// Fin pages non home
	}

		if ((!$aff_specifique)||(($aff_specifique)&&(($tous_struct)||($tous_evts)||($tous_pa))))
		{
			?>
			<script type="text/javascript">
			$(function() {
					$(".ferme-colorbox").live("click", function(){
						$.colorbox.close();
					});
					$('.ico-like-list').click(function() {
						if (!$(this).hasClass("desactive"))
						{
							var obj = $(this).get();
							var id_recu = $(this).attr('rel');
							var type = $(this).attr("id");
							var urlpage = $(this).attr("href");
							var nb_like = $(this).attr("name");
							param = 'id_recu='+id_recu+'&type='+type+'&nb_like='+nb_like+'&urlpage='+urlpage;
							$(this).addClass('desactive');
							<?php  // attention, ajouter aussi un case à ajax_like.php ?>
							switch (type) {
								case "structure":
								type_aimez = "Cette structure fait partie de vos coups de coeur";
								break;
								case "evenement":
								type_aimez = "Cet évènement fait partie de vos coups de coeur";
								break;
							}
							$.colorbox({
							  href:"ajax_like.php",
							  width:"550px",
							  data:param,
							  onComplete : function() {
								$(this).colorbox.resize();
								nb_like++;
								$(obj).children(".nb-like").text(nb_like);
								$(obj).attr("name",nb_like);
								$(obj)[0].title = type_aimez;
								$(obj).poshytip({
									content: type_aimez,
									showOn: 'none'
								});
							  }
							});
						}
						return false;
					});
					$('.ico-fav-list').live("click", function(){
						var obj = $(this).get();
						var id_recu = $(this).attr('rel');
						var type = $(this).attr("id");
						var urlpage = $(this).attr("href");
						param = 'id_recu='+id_recu+'&type='+type+'&urlpage='+urlpage;
						param2 = param.concat('&action=supprime') ;
						if ((!$(this).hasClass("desactive")) && (!$(this).hasClass("supprime")))
						{
							<?php  // attention, ajouter aussi un case à ajax_like.php ?>
							switch (type) {
								case "structure":
								var type_fav = "Cette structure est actuellement dans vos archives";
								break;
								case "evenement":
								var type_fav = "Cet évènement est actuellement dans vos archives";
								break;
							}
							if (!$(this).hasClass("connect"))
							{
								$(this).addClass('desactive');
								$(this).addClass('supprime');
							}
							else
							{
								var type_fav = "Connectez vous avant de pouvoir ajouter aux archives";
							}
							$.colorbox({
							  href:"ajax_fav.php",
							  width:"550px",
							  data:param,
							  onComplete : function() {
								$(this).colorbox.resize();
								$(obj)[0].title = type_fav;
								$(obj).poshytip({
									content: type_fav,
									showOn: 'none'
								});
							  }
							});
						} else if($(this).hasClass("supprime")) {
							<?php  // attention, ajouter aussi un case à ajax_like.php ?>
							switch (type) {
								case "structure":
								var type_fav = "Archiver cette structure";
								break;
								case "evenement":
								var type_fav = "Arhiver cet évènement";
								break;
							}
							$.colorbox({
							  href:"ajax_fav.php",
							  width:"550px",
							  data:param2,
							  onComplete : function() {
								$(this).colorbox.resize();
								$(obj)[0].title = type_fav;
								$(obj).poshytip({
									content: type_fav,
									showOn: 'none'
								});
							  }
							});	
						}
						return false;	
					});
			});
			</script>
  <?php } ?>
	<script type="text/javascript">
		$(function() {
				$("#contenu-index").hide();
		});
		$(window).load(function(){
				$("#load").fadeOut()
				$("#contenu-index").fadeIn()
		});
	</script>

<?php
	// Footer
	include ('01_include/structure_footer.php');
?>
