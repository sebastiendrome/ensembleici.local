<?php
	session_name("EspacePerso");
	session_start();
	require ('01_include/_var_ensemble.php');
	require ('01_include/_connect.php');

	$operateur="AND";
	$tab_mots_prepares = array();

	$chaine_recherche=strtolower(trim($_POST['mot']));                	//on passe en minuscule
	$mots = str_replace("+", " ", trim($chaine_recherche));  	//on remplace les + par des espaces
	$mots = str_replace("\"", " ", $mots);               	//idem pour \
	$mots = str_replace("'", " ", $mots);               	//idem pour '
	$mots = str_replace(",", " ", $mots);               	//idem pour ,
	$mots = str_replace(":", " ", $mots);               	//idem pour :
	$chaine_recherche=rawurlencode($chaine_recherche); //on encode la recherche   
	$tab=explode(" ", $mots);
	$nb=count($tab);

	if(strlen($chaine_recherche)>3)
	{
		// Préparation requête (& recherche du 1er mot)

		// Structures
		$sql_structure="SELECT S.* FROM structure S
			LEFT JOIN `villes` V ON S.no_ville = V.id
					WHERE S.etat = 1
					AND (
						nom like :mot_rechercher
						OR sous_titre like :mot_rechercher
						OR description like :mot_rechercher
						OR nom_ville_url like :mot_rechercher
					)";

		// Evènements
		$sql_evenement="SELECT E.* FROM evenement E
			LEFT JOIN `villes` V ON E.no_ville = V.id
			LEFT JOIN `genre` G ON E.no_genre = G.no
					WHERE E.etat=1
					AND date_fin>=CURDATE()
					AND (
						titre like :mot_rechercher
						OR sous_titre like :mot_rechercher
						OR description like :mot_rechercher
						OR description_complementaire like :mot_rechercher
						OR nom_ville_url like :mot_rechercher
						OR libelle like :mot_rechercher
					)";

		// Tags
		$sql_tag="SELECT *, 'tag' AS type FROM tag WHERE titre like :mot_rechercher";

		// Sous-tags
		$sql_sstag="SELECT *, 'sstag' AS type FROM `sous_tag` WHERE titre like :mot_rechercher";

		// Villes (toutes les villes contenant des evts, structuress, PA....)
		$sql_villes="
		  SELECT *
		  FROM (
		    (SELECT V.* FROM `petiteannonce` PA, `villes` V WHERE PA.etat=1 AND PA.no_ville=V.id)
		       UNION
		    (SELECT V.* FROM `evenement` E, `villes` V WHERE E.date_fin>=CURDATE() AND E.etat=1 AND E.no_ville=V.id)
		       UNION
		    (SELECT V.* FROM `structure` S, `villes` V WHERE S.etat=1 AND S.no_ville=V.id)
		  ) AS U1
			WHERE U1.nom_ville_url like :mot_rechercher
		  ";
		$tab_mots_prepares[":mot_rechercher"] = "%".$tab[0]."%";

		// Ajout des mots recherchés (si + de 1)
		for($i=1 ; $i<$nb; $i++)
		{
			if(strlen($tab[$i])>3)
			{
				$sql_structure.=" $operateur (
						nom like :mot_rechercher$i
						OR sous_titre like :mot_rechercher$i
						OR description like :mot_rechercher$i
						OR nom_ville_url like :mot_rechercher$i
					)";
				$sql_evenement.=" $operateur (
						titre like :mot_rechercher$i
						OR sous_titre like :mot_rechercher$i
						OR description like :mot_rechercher$i
						OR description_complementaire like :mot_rechercher$i
						OR nom_ville_url like :mot_rechercher$i
						OR libelle like :mot_rechercher$i
					)";
				$sql_tag.=" $operateur titre like :mot_rechercher$i";
				$sql_sstag.=" $operateur titre like :mot_rechercher$i";
				$sql_villes.=" OR U1.nom_ville_url like :mot_rechercher$i"; // Tjrs en OR

				$tab_mots_prepares[":mot_rechercher$i"] = "%".$tab[$i]."%";
			}
		}

		$sql_structure .= " AND no_ville<>0 ORDER BY nom";
		$res_structure = $connexion->prepare($sql_structure);
		$res_structure->execute($tab_mots_prepares) or die ("Erreur ".__LINE__ ." : ".$sql_structure);
		$tab_structure=$res_structure->fetchAll();
		$nb_st = count($tab_structure);
	
		$sql_evenement .= " AND no_ville<>0 ORDER BY date_debut";
		$res_evenement = $connexion->prepare($sql_evenement);
		$res_evenement->execute($tab_mots_prepares) or die ("Erreur ".__LINE__ ." : ".$sql_evenement);
		$tab_evenement=$res_evenement->fetchAll();
		$nb_et = count($tab_evenement);
		
		// Tags
		$res_tag = $connexion->prepare($sql_tag);
		$res_tag->execute($tab_mots_prepares) or die ("Erreur ".__LINE__ ." : ".$sql_tag);
		$tab_tag=$res_tag->fetchAll();
		$nb_tags = count($tab_tag);

		// Ss-tags
		$res_sstag = $connexion->prepare($sql_sstag);
		$res_sstag->execute($tab_mots_prepares) or die ("Erreur ".__LINE__ ." : ".$sql_sstag);
		$tab_sstag=$res_sstag->fetchAll();
		$nb_sstags = count($tab_sstag);

		// Villes
		$sql_villes .= " GROUP BY U1.id"; // Evier les doublons
		$res_ville = $connexion->prepare($sql_villes);
		$res_ville->execute($tab_mots_prepares) or die ("Erreur ".__LINE__ ." : ".$sql_villes);
		$tab_villes = $res_ville->fetchAll();
		$nb_villes = count($tab_villes);
	}
	
	// include header
	$titre_page = "Recherche";
	$meta_description = "Recherche sur Ensemble ici : Tous acteurs de la vie locale";
	$ajout_header .= <<<AJHE
AJHE;
	include ('01_include/structure_header.php');
	echo "<div id=\"colonne2\" class=\"page_ville\">";

			if(strlen($chaine_recherche)>3)
			{
				echo "<p>Votre recherche : &laquo; ".$mots." &raquo;</p>";

				// Villes
				if ($nb_villes)
				{
					echo "<div id=\"bandes-tags\" class=\"blocA\">";
					echo "<h4>Ville".($nb_villes>1?"s":"")." correpondant à votre recherche :</h4>";
					for($indice_ville=0; $indice_ville<$nb_villes; $indice_ville++)
					{
						if ( ($tab_villes[$indice_ville]["id"]) && ($tab_villes[$indice_ville]["nom_ville_url"]) )
						{

					      // buis-les-baronnies.9424.choix.html
					      $lienville = $tab_villes[$indice_ville]["nom_ville_url"].".".$tab_villes[$indice_ville]["id"].".choix.html";
					      echo "<p><a href=\"".$lienville."\" title=\"".$tab_villes[$indice_ville]["nom_ville_maj"]."\">".$tab_villes[$indice_ville]["nom_ville_maj"].", ".$tab_villes[$indice_ville]["code_postal"]."</a></p>";
						}
					}
					echo "</div>";
					echo "<div class=\"clear\"></div>";
				}

				// Tags
				if ($nb_tags)
				{
					echo "<div id=\"bandes-tags\" class=\"blocA\">";
					echo "<h4>Thématique".($nb_tags>1?"s":"")." correpondant à votre recherche :</h4>";
	
					for($indice_tag=0; $indice_tag<$nb_tags; $indice_tag++)
					{
						// recuperation de la ville (depuis le cookie ou par défaut)
						if (($_COOKIE["id_ville"])&&(!empty($_COOKIE["id_ville"])))
							$id_ville = intval($_COOKIE["id_ville"]);
						else
							$id_ville = $id_ville_defaut;
						$sql_villes="SELECT * FROM villes WHERE id=:no";
						$res_villes = $connexion->prepare($sql_villes);
						$res_villes->execute(array(':no'=>$id_ville)) or die ("Erreur ".__LINE__ ." : ".$sql_villes);
						$tab_villes=$res_villes->fetchAll();
	
						// Pour l'instant uniquement les tags
						if ($tab_tag[$indice_tag]["type"]=="tag")
						{
							if ((!empty($tab_tag[$indice_tag]["no"]))||(!empty($tab_tag[$indice_tag]["titre"])))
							{
							    if ($id_tag==$tab_tag[$indice_tag]["no"]) $tag_actif = " boutonbleuactif";
							    $lien = $tab_villes[0]["nom_ville_url"].".".url_rewrite($tab_tag[$indice_tag]["titre"]).".tag.".$id_ville.".".$tab_tag[$indice_tag]["no"].".html";
							    echo "<a href=\"".$lien."\" title=\"Afficher ".$tab_tag[$indice_tag]["titre"]."\" class=\"boutonbleu$tag_actif\">".$tab_tag[$indice_tag]["titre"]."</a>";
							}
						}
					}
					echo "</div>";
				}
				echo "<div class=\"clear\"></div>";
				
				// Evènements
				echo "<div id=\"agendalocal\" class=\"blocB\">";
				echo "<h1>Evènements</h1>";

				if($nb_et)
				{
					echo "<p class=\"nb_evts\">";
					echo $nb_et." évènement";
					if($nb_et>1) echo "s";
					echo " correspondant à votre recherche :</p>";
					echo "<div class=\"clear\"></div>";
				}
				else
				{
					echo "<p class=\"nb_evts\">Aucun évènement ne correspond à votre recherche.</p>";
				}

				for($indice_event=0; $indice_event<$nb_et; $indice_event++)
				{
					$titre_pour_lien = coupe_chaine($tab_evenement[$indice_event]["titre"],130,false);
					
					//recuperation du genre
					$sql_genre="SELECT * FROM genre WHERE no=:no";
					$res_genre = $connexion->prepare($sql_genre);
					$res_genre->execute(array(':no'=>$tab_evenement[$indice_event]['no_genre'])) or die ("Erreur ".__LINE__ ." : ".$sql_genre);
					$tab_genre=$res_genre->fetchAll();
					//recuperation de la ville
					$sql_villes="SELECT * FROM villes WHERE id=:no";
					$res_villes = $connexion->prepare($sql_villes);
					$res_villes->execute(array(':no'=>$tab_evenement[$indice_event]['no_ville'])) or die ("Erreur ".__LINE__ ." : ".$sql_villes);
					$tab_villes=$res_villes->fetchAll();

					if ($tab_villes[0]["nom_ville_maj"])
					{
						echo "<div class=\"un-event\">";
						if ($tab_genre[0]["libelle"])
						{
							echo "<div class=\"genre_ville\">";
							echo "<div class=\"libelle_genre\">".$tab_genre[0]["libelle"]."</div>";
							echo "<div class=\"ville\">".$tab_villes[0]["nom_ville_maj"]."</div>";
							echo "</div>";
							$titre_pour_lien = $tab_genre[0]["libelle"]."-".$titre_pour_lien;
						}
						// Lien vers le détails de l'évenement. 
						$lien = "evenement.".$tab_villes[0]["nom_ville_url"].".".url_rewrite($titre_pour_lien).".".$tab_villes[0]["id"].".".$tab_evenement[$indice_event]["no"].".html";
						// Titre
						echo "<a href=\"".$lien."\" title=\"Voir en détails\">";
						echo "<h2>";
						if ($tab_evenement[$indice_event]["date_debut"])
						{
						  if (($tab_evenement[$indice_event]["date_fin"])&&($tab_evenement[$indice_event]["date_debut"]!=$tab_evenement[$indice_event]["date_fin"])){
							echo "<span>Du ".datefr($tab_evenement[$indice_event]["date_debut"])." au ".datefr($tab_evenement[$indice_event]["date_fin"])." : </span>";
						  }
						  else
						  {
							echo "<span>".datefr($tab_evenement[$indice_event]["date_debut"])." : </span>";
						  }
						}
						echo $tab_evenement[$indice_event]["titre"]."</h2></a>";
						// Image
						if (($tab_evenement[$indice_event]["url_image"])&&(file_exists(realpath($tab_evenement[$indice_event]["url_image"]))))
						{
						  echo "<div class=\"illustr\">
						  <a href=\"".$lien."\" title=\"Voir en détails\">
						  <img src=\"miniature.php?uri=".$tab_evenement[$indice_event]["url_image"]."&method=fit&w=80".
						  
						  "\" alt=\"".str_replace("\"","'",$tab_evenement[$indice_event]["titre"])."\" width=\"80\" /></a>".
						  "</div>";
						}			
						echo "<p><strong>".ucfirst($tab_evenement[$indice_event]["sous_titre"])."</strong></p>";
						echo "<p>".coupe_chaine($tab_evenement[$indice_event]["description"],250,true)."</p>";  // Afficher 250 carractères de la description
						echo "<div class=\"actions\">";
						echo "<a href=\"".$lien."\" title=\"Voir en détails\" class=\"boutonrouge ico-loupe-rge\">Voir</a>";
						echo "</div>"; // actions
						echo "</div>"; // un-event
						echo "<br/>";
					}
				}

				echo "<div class=\"clear\"></div>";
				echo "</div>";
		

				
				// Structures

				echo "<div id=\"repertoire\" class=\"blocB\">";
				echo "<h1>Structures</h1>";


				// Ss-Tags
				if ($nb_sstags)
				{
					echo "<div id=\"bandes-tags\" class=\"blocA\">";
					echo "<h4>Thématique".($nb_sstags>1?"s":"")." correpondant à votre recherche :</h4>";
	
					for($indice_sstag=0; $indice_sstag<$nb_sstags; $indice_sstag++)
					{
						// recuperation de la ville (depuis le cookie ou par défaut)
						if (($_COOKIE["id_ville"])&&(!empty($_COOKIE["id_ville"])))
							$id_ville = intval($_COOKIE["id_ville"]);
						else
							$id_ville = $id_ville_defaut;
						$sql_villes="SELECT * FROM villes WHERE id=:no";
						$res_villes = $connexion->prepare($sql_villes);
						$res_villes->execute(array(':no'=>$id_ville)) or die ("Erreur ".__LINE__ ." : ".$sql_villes);
						$tab_villes=$res_villes->fetchAll();
	
						// Pour l'instant uniquement les tags
						if ($tab_sstag[$indice_sstag]["type"]=="sstag")
						{
							if ((!empty($tab_sstag[$indice_sstag]["no"]))||(!empty($tab_sstag[$indice_sstag]["titre"])))
							{
							    if ($id_tag==$tab_sstag[$indice_sstag]["no"]) $tag_actif = " boutonbleuactif";
							    $lien = $tab_villes[0]["nom_ville_url"].".".url_rewrite(trim($tab_sstag[$indice_sstag]["titre"])).".sstag.".$id_ville.".".$tab_sstag[$indice_sstag]["no"].".html";
							    echo "<a href=\"".$lien."\" title=\"Afficher ".$tab_sstag[$indice_sstag]["titre"]."\" class=\"boutonbleu$tag_actif\">".$tab_sstag[$indice_sstag]["titre"]."</a>";
							}
						}
					}
					echo "</div>";
				}
				echo "<div class=\"clear\"></div>";
				
				if($nb_st) 
				{
					echo "<p class=\"nb_structs\">";
					echo $nb_st." structure";
					if($nb_st>1) echo "s";
					echo " correspondant à votre recherche :</p>";
					echo "<div class=\"clear\"></div>";
				}
				else
				{
					echo "<p class=\"nb_structs\">Aucune structure ne correspond à votre recherche.</p>";	
				}

				//liste pour titre
				for($indice_structure=0; $indice_structure<$nb_st; $indice_structure++)
				{
					// Nom de la structure coupé à 130 carractères pour le lien
					$titre_pour_lien = coupe_chaine($tab_structure[$indice_structure]["nom"],130,false);
					
					//recuperation du statut
					$sql_statut="SELECT * FROM statut WHERE no=:no";
					$res_statut = $connexion->prepare($sql_statut);
					$res_statut->execute(array(':no'=>$tab_structure[$indice_structure]['no_statut'])) or die ("Erreur ".__LINE__ ." : ".$sql_statut);
					$tab_statut=$res_statut->fetchAll();
					//recuperation de la ville
					$sql_villes="SELECT * FROM villes WHERE id=:no";
					$res_villes = $connexion->prepare($sql_villes);
					$res_villes->execute(array(':no'=>$tab_structure[$indice_structure]['no_ville'])) or die ("Erreur ".__LINE__ ." : ".$sql_villes);
					$tab_villes=$res_villes->fetchAll();
					
					echo "<div class=\"une-struct\">";
					
					if ($tab_statut[0]["libelle"])
					{
					  echo "<div class=\"genre_ville\">";
						echo "<div class=\"libelle_statut\">".$tab_statut[0]["libelle"]."</div>";
						echo "<div class=\"ville\">".$tab_villes[0]["nom_ville_maj"]."</div>";
					  echo "</div>";
					  // Ajout du genre à l'url
					  $titre_pour_lien = $tab_statut[0]["libelle"]."-".$titre_pour_lien;
					}
					
					// Lien vers le détails de la structure 
					$lien = "structure.".$tab_villes[0]["nom_ville_url"].".".url_rewrite($titre_pour_lien).".".$tab_villes[0]["id"].".".$tab_structure[$indice_structure]['no'].".html";

					// Titre
					echo "<a href=\"".$lien."\" title=\"Voir en détails\">";
					echo "<h2>".ucfirst($tab_structure[$indice_structure]["nom"]);
					echo "</h2>";
					echo "</a>";
					if ($tab_structure[$indice_structure]["sous_titre"]) echo "<p><strong>".ucfirst($tab_structure[$indice_structure]["sous_titre"])."</strong></p>";
					echo "</div>";
					echo "<br/>";
				}
				
				echo "</div>";
			}
			else
			{
				echo "<p>Mot recherché trop court...</p>";
			}

		echo "</div>";
		$affiche_articles = true;
		$affiche_publicites = true;
		include ('01_include/structure_colonne3.php');
		echo "<div class=\"clear\"></div>";
	?>

	<script src="js/jquery.validationEngine-fr.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
	<script type="text/javascript">
		$(document).ready(function() {
		    // Validation form
		    $("#recherche_form").validationEngine("attach",{promptPosition : "topRight", scroll: false});
		});
	</script>
<?php
include ('01_include/structure_footer.php');
?>