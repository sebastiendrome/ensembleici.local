<script type="text/javascript" src="js/jquery.poshytip.min.js"></script>
<script type="text/javascript" src="js/commun.js"></script>
<?php
$id_utilisateur = $_SESSION['UserConnecte_id']; // Id utilisateur

require_once ('_connect.php');
require_once ('_var_ensemble.php');


// Les différents types à afficher
$les_types = array ( 	0 => array ( "type" => "evenement",
		                  			 "presentation" => "img",
                                     "titre_bloc" => "Vos évènements archivés :"
                                   ),
                  		1 => array ( "type" => "structure",
		                  			 "presentation" => "txt",
                                     "titre_bloc" => "Vos structures archivées :"
                                   ),
                  		2 => array ( "type" => "petiteannonce",
		                  			 "presentation" => "txt",
                                     "titre_bloc" => "Vos petites annonces archivées :"
                                   )
                	);

for ($i = 0; $i<count($les_types); $i++)
{
	$type = $les_types[$i]["type"];
	$titre_bloc = $les_types[$i]["titre_bloc"];
	$presentation = $les_types[$i]["presentation"];
	if ($presentation=="txt") $mode_txt = true;

	echo "<div class=\"blocC affiche_result\">";
	echo "<h4>$titre_bloc</h4>";
	
	$req = "SELECT * FROM $type O, favori F WHERE O.no=F.no_occurence AND F.no_utilisateur=$id_utilisateur AND type_fav='$type' AND etat=1";
    $resultats= $connexion->query($req);
	$lignes = $resultats->rowCount();
	
	if($mode_txt && $lignes) echo "<ul>";
    
    $resultats->setFetchMode(PDO::FETCH_OBJ);
	if($lignes)
	{
		while( $occurence = $resultats->fetch() ) // on récupère la liste des membres
		{
			// Init variables
			$imgsrc = $url_image = "";
			$url_image_locale = true;

			// Champs identiques partout
			$no_objet = $occurence->no;
			$no_ville = $occurence->no_ville;
			$description = $occurence->description;
	
			// Champs spécifiques
			switch ($type) {
				case "structure":
					$titre = $occurence->nom;
					// $url_image = $occurence->url_logo;
				break;
				case "evenement":
					$titre = $occurence->titre;
					$no_genre = $occurence->no_genre;
					$url_image = $occurence->url_image;
					$date = $occurence->date_debut;
					$titre_infob = $date."<br />".str_replace('"',"'",$titre);
				break;
				case "petiteannonce":
					$titre = $occurence->titre;
					// $url_image = $occurence->url_image;
				break;
			}
			
			// Récup de la ville
			if ($no_ville) 
			{
				$sql_ville="SELECT nom_ville_maj, nom_ville_url, code_postal
						  FROM `villes`
						  WHERE id=:no_ville";
				$resville = $connexion->prepare($sql_ville);
				$resville->execute(array(':no_ville'=>$no_ville)) or die ("Erreur 52 : ".$sql_ville);
				$rowville=$resville->fetchAll();
				$nom_ville_url = $rowville[0]['nom_ville_url'];
			}
			
			// Récup du genre (evts)
			if($no_genre)
			{
				$sql_genre="SELECT libelle
						  FROM `genre`
						  WHERE no=:no_genre";
				$resgenre = $connexion->prepare($sql_genre);
				$resgenre->execute(array(':no_genre'=>$no_genre)) or die ("Erreur 61 : ".$sql_genre);
				$rowgenre=$resgenre->fetchAll();
				$nom_genre = $rowgenre[0]['libelle'];
			}
			
			$titre_pour_lien = coupe_chaine($titre,130,false);
			if ($nom_genre)
			{
				$titre_pour_lien = $nom_genre."-".$titre_pour_lien;
				// Infobulle sur l'img
				$titre_infob .= "<br />".str_replace('"',"'",$nom_genre);
			}
					  
			$lien_voir = strtolower($root_site.$type.".".$nom_ville_url.".".url_rewrite($titre_pour_lien).".".$no_ville.".".$no_objet.".html");
			if($mode_txt)
			{
				echo "<li><a href=\"$lien_voir\" name=\"détails\" >$titre</a></li><br />";
			}
			else
			{
				// Affichage de l'image
				if(strlen($url_image)>0)
				{
					// image distante ?
					if (strpos($url_image, "http://www.culture-provence-baronnies.fr") !== false)
					{
						$imgsrc = $url_image;
						$url_image = "";
						$url_image_locale = false;
					}
					else
					{
					// Image locale ?
						// Image stockée sur la version de dev ou sur le site en prod
						$fileUrl = $root_site.$url_image;
						$AgetHeaders = @get_headers($fileUrl);

						if (preg_match("|200|", $AgetHeaders[0]))
							$root_site_d = $root_site; // fichier existant
						else
						{
							// fichier non existant => on essaie l'autre chemin
							if ($root_site == $root_site_dev)
								$root_site_d = $root_site_prod;
							else
								$root_site_d = $root_site_dev;
							// Image toujours non existante ?
							$fileUrl = $root_site_d.$url_image;
							$AgetHeaders = @get_headers($fileUrl);
							// fichier inexistant => image par défaut
							if (!(preg_match("|200|", $AgetHeaders[0])))
								$url_image = "img/img_defaut.png";
						}
					}
				}

				
				// Image locale ok ?
				if ($url_image_locale)
				{
					if (!$url_image)
						$url_image = "img/img_defaut.png";
					$imgsrc = $root_site_d."miniature.php?uri=".$url_image."&method=fit&w=80";
				}


				// Affichage de l'image
				if ($imgsrc)
				{
					echo "<div class=\"illustr\">";
					echo "<a href=\"$lien_voir\" name=\"détails\"><img src=\"".$imgsrc."\" class=\"infobulle-b\" title=\"$titre_infob\" width=\"80\"/></a>";
					echo "</div>";
				}
			} // Fin mode txt ou img
		}
	}
	else
	{
		// Aucun résultat
		switch ($type) {
			case "evenement":
			$lieu = "l'agenda";
			$type_phrase = "évènements préférés";
			break;
			
			case "structure":
			$lieu = "le répertoire";
			$type_phrase = "structures préférées";
			break;

			case "petiteannonce":
			$lieu = "les petites annonces";
			$type_phrase = "petites annonces préférées";
			break;
		}
		
		echo "<p>Cliquez sur <img src=\"img/fav-text.png\" /> pour retrouver à tout moment vos $type_phrase dans votre espace personnel.</p>";
 
	}
	if ($mode_txt && $lignes) echo "</ul>";
    echo "<br class=\"clear\" />";
	$resultats->closeCursor(); // on ferme le curseur des résultats	
	echo "</div>";
}
echo "<p class=\"note\"><em>Rendez-vous sur les pages dédiées pour supprimer un élément de vos archives.</em></p>";
?>